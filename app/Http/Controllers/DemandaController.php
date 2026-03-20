<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\Condominio;
use App\Models\Prestador;
use App\Models\DemandaAnexo;
use App\Models\Orcamento;
use App\Models\Negociacao;
use App\Models\Tag;
use App\Models\LinkDemandaPublico;
use App\Helpers\ValidacaoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewDemandNotification;
use App\Notifications\NegotiationRequestedNotification;
use App\Notifications\BudgetStatusNotification;
use App\Models\NotificationSetting;
use App\Channels\WhatsAppChannel;

class DemandaController extends Controller
{
    public function index(Request $request)
    {
        $query = Demanda::with(['condominio', 'categoriaServico', 'usuario']);

        // Filtro por pesquisa (título ou descrição)
        if ($request->filled('pesquisa')) {
            $pesquisa = $request->pesquisa;
            $query->where(function($q) use ($pesquisa) {
                $q->where('titulo', 'like', "%{$pesquisa}%")
                  ->orWhere('descricao', 'like', "%{$pesquisa}%");
            });
        }

        // Filtro por status
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filtro por condomínio
        if ($request->filled('condominio_id')) {
            $query->where('condominio_id', $request->condominio_id);
        }

        // Filtro por urgência
        if ($request->filled('urgencia') && $request->urgencia !== 'todos') {
            $query->where('urgencia', $request->urgencia);
        }

        $demandas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Prepara dados dos condomínios para JavaScript (autocomplete)
        $condominiosData = Condominio::ativos()
            ->select('id', 'nome', 'bairro', 'cidade')
            ->orderBy('nome')
            ->get();

        return view('demandas.index', compact('demandas', 'condominiosData'));
    }

    public function create()
    {
        $this->authorize('create', Demanda::class);

        $condominios = Condominio::ativos()->with('tags')->orderBy('nome')->get();
        $condominiosData = $condominios->map(fn($c) => [
            'id' => $c->id,
            'nome' => $c->nome,
            'bairro' => $c->bairro,
            'cidade' => $c->cidade
        ]);
            
        $prestadores = Prestador::ativos()->with('tags')->orderBy('nome_razao_social')->get();

        $tags = Tag::ativas()->orderBy('ordem')->orderBy('nome')->get();

        return view('demandas.create', compact('condominios', 'condominiosData', 'prestadores', 'tags'));
    }


    public function store(Request $request)
    {
        $this->authorize('create', Demanda::class);

        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'urgencia' => 'nullable|in:baixa,media,alta,critica',
            'prestadores' => 'nullable|array',
            'prestadores.*' => 'exists:prestadores,id',
            'anexos.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,pdf|max:10240',
        ]);

        $user = Auth::user();
        $validated['administradora_id'] = $user->administradora_id;
        $validated['usuario_id'] = $user->id;
        $validated['status'] = 'aberta';

        $demanda = Demanda::create($validated);

        // Processa anexos
        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $arquivo) {
                $nomeArquivo = Str::uuid() . '.' . $arquivo->getClientOriginalExtension();
                $caminho = $arquivo->storeAs('demandas/anexos', $nomeArquivo, 'public');

                DemandaAnexo::create([
                    'demanda_id' => $demanda->id,
                    'administradora_id' => $user->administradora_id,
                    'nome_original' => $arquivo->getClientOriginalName(),
                    'nome_arquivo' => $nomeArquivo,
                    'caminho' => $caminho,
                    'mime_type' => $arquivo->getMimeType(),
                    'tamanho' => $arquivo->getSize(),
                ]);
            }
        }

        // Associa prestadores e gera links
        if ($request->has('prestadores')) {
            $demanda->prestadores()->attach($request->prestadores, ['status' => 'convidado']);
            \App\Http\Controllers\LinkPrestadorController::gerarLinksParaDemanda($demanda, $request->prestadores);
        }

        return redirect()->route('demandas.index')
            ->with('success', 'Demanda criada com sucesso!');
    }

    public function show(Demanda $demanda)
    {
        $this->authorize('view', $demanda);

        $demanda->load([
            'condominio', 
            'usuario', 
            'prestadores', 
            'orcamentos.negociacoes', 
            'orcamentos.prestador',
            'orcamentos.concluidoPor',
            'orcamentos.documentos',
            'links', 
            'linksPublicos', 
            'negociacoes', 
            'anexos'
        ]);

        $prestadoresIds = $demanda->prestadores->pluck('id')->toArray();
        $prestadoresDisponiveis = Prestador::ativos()
            ->whereNotIn('id', $prestadoresIds)
            ->orderBy('nome_razao_social')
            ->get();

        return view('demandas.show', compact('demanda', 'prestadoresDisponiveis'));
    }

    public function edit(Demanda $demanda)
    {
        $this->authorize('update', $demanda);

        $condominios = Condominio::ativos()->with('tags')->orderBy('nome')->get();
        $condominiosData = $condominios->map(fn($c) => [
            'id' => $c->id,
            'nome' => $c->nome,
            'bairro' => $c->bairro,
            'cidade' => $c->cidade
        ]);
        
        $prestadores = Prestador::ativos()->with('tags')->orderBy('nome_razao_social')->get();
        $tags = Tag::ativas()->orderBy('ordem')->orderBy('nome')->get();
        $prestadoresSelecionados = $demanda->prestadores->pluck('id')->toArray();

        return view('demandas.edit', compact('demanda', 'condominios', 'condominiosData', 'prestadores', 'tags', 'prestadoresSelecionados'));
    }


    public function update(Request $request, Demanda $demanda)
    {
        $this->authorize('update', $demanda);

        $validated = $request->validate([
            'condominio_id' => 'required|exists:condominios,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'urgencia' => 'nullable|in:baixa,media,alta,critica',
            'observacoes' => 'nullable|string',
            'prestadores' => 'nullable|array',
            'prestadores.*' => 'exists:prestadores,id',
            'anexos.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,pdf|max:10240',
        ]);

        $demanda->update($validated);

        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $arquivo) {
                $nomeArquivo = Str::uuid() . '.' . $arquivo->getClientOriginalExtension();
                $caminho = $arquivo->storeAs('demandas/anexos', $nomeArquivo, 'public');

                DemandaAnexo::create([
                    'demanda_id' => $demanda->id,
                    'administradora_id' => Auth::user()->administradora_id,
                    'nome_original' => $arquivo->getClientOriginalName(),
                    'nome_arquivo' => $nomeArquivo,
                    'caminho' => $caminho,
                    'mime_type' => $arquivo->getMimeType(),
                    'tamanho' => $arquivo->getSize(),
                ]);
            }
        }

        if ($request->has('prestadores')) {
            $prestadoresAtuais = $demanda->prestadores->pluck('id')->toArray();
            $prestadoresNovos = $request->prestadores;
            
            $demanda->prestadores()->detach(array_diff($prestadoresAtuais, $prestadoresNovos));
            
            $paraAdicionar = array_diff($prestadoresNovos, $prestadoresAtuais);
            if (!empty($paraAdicionar)) {
                $demanda->prestadores()->attach($paraAdicionar, ['status' => 'convidado']);
                \App\Http\Controllers\LinkPrestadorController::gerarLinksParaDemanda($demanda, $paraAdicionar, true);
            }
        }

        return redirect()->route('demandas.show', $demanda)
            ->with('success', 'Demanda atualizada com sucesso!');
    }

    public function updateStatus(Request $request, Demanda $demanda)
    {
        $this->authorize('update', $demanda);

        $validated = $request->validate([
            'status' => 'required|in:aberta,em_andamento,aguardando_orcamento,concluida,cancelada',
        ]);

        $demanda->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status atualizado!');
    }

    public function aprovarOrcamento(Request $request, Demanda $demanda, $orcamento)
    {
        $this->authorize('update', $demanda);

        $orcamento = Orcamento::where('demanda_id', $demanda->id)->findOrFail($orcamento);

        DB::transaction(function() use ($orcamento, $demanda, $request) {
            Orcamento::where('demanda_id', $demanda->id)
                ->where('id', '!=', $orcamento->id)
                ->where('status', '!=', 'rejeitado')
                ->update([
                    'status' => 'rejeitado',
                    'motivo_rejeicao' => 'Outro orçamento foi aprovado.',
                ]);

            $orcamento->update([
                'status' => 'aprovado',
                'aprovado_por' => Auth::id(),
                'aprovado_em' => now(),
                'observacoes' => $request->observacoes ?? $orcamento->observacoes,
            ]);

            $demanda->update(['status' => 'em_andamento']);
        });

        // Notifica o prestador se a configuração estiver ativa
        $shouldNotify = NotificationSetting::where('key', 'notify_budget_status')->first()?->value === '1';
        if ($shouldNotify && $orcamento->prestador) {
            $url = route('prestador.link.show', $orcamento->link_prestador_id ? $orcamento->linkPrestador->token : ($orcamento->linkPublico ? $orcamento->linkPublico->token : ''));
            $orcamento->prestador->notify(new BudgetStatusNotification($orcamento, 'aprovado', $url));
        }

        return redirect()->back()->with('success', 'Orçamento aprovado!');
    }

    public function adicionarPrestador(Request $request, Demanda $demanda)
    {
        $this->authorize('update', $demanda);

        $validated = $request->validate([
            'prestadores' => 'required|array',
            'prestadores.*' => 'exists:prestadores,id',
        ]);

        $demanda->prestadores()->syncWithoutDetaching($validated['prestadores']);
        
        \App\Http\Controllers\LinkPrestadorController::gerarLinksParaDemanda($demanda, $validated['prestadores'], true);

        return redirect()->back()->with('success', 'Prestadores adicionados à demanda!');
    }

    public function removerPrestador(Demanda $demanda, Prestador $prestador)
    {
        $this->authorize('update', $demanda);

        $demanda->prestadores()->detach($prestador->id);

        return redirect()->back()->with('success', 'Prestador removido da demanda!');
    }

    public function rejeitarOrcamento(Request $request, Demanda $demanda, $orcamento)
    {
        $this->authorize('update', $demanda);

        $orcamento = Orcamento::where('demanda_id', $demanda->id)->findOrFail($orcamento);

        $request->validate([
            'motivo_rejeicao' => 'required|string',
        ]);

        $orcamento->rejeitar($request->motivo_rejeicao);

        // Notifica o prestador se a configuração estiver ativa
        $shouldNotify = NotificationSetting::where('key', 'notify_budget_status')->first()?->value === '1';
        if ($shouldNotify && $orcamento->prestador) {
            $url = route('prestador.link.show', $orcamento->link_prestador_id ? $orcamento->linkPrestador->token : ($orcamento->linkPublico ? $orcamento->linkPublico->token : ''));
            $orcamento->prestador->notify(new BudgetStatusNotification($orcamento, 'rejeitado', $url));
        }

        return redirect()->back()->with('success', 'Orçamento rejeitado.');
    }

    public function criarNegociacao(Request $request, Demanda $demanda, $orcamento)
    {
        $this->authorize('update', $demanda);

        $orcamento = Orcamento::where('demanda_id', $demanda->id)->findOrFail($orcamento);

        $validated = $request->validate([
            'tipo' => 'required|in:desconto,parcelamento,contraproposta',
            'valor_solicitado' => 'nullable|numeric|min:0',
            'descricao' => 'required|string',
        ]);

        $negociacao = Negociacao::create([
            'demanda_id' => $demanda->id,
            'orcamento_id' => $orcamento->id,
            'prestador_id' => $orcamento->prestador_id,
            'tipo' => $validated['tipo'],
            'valor_original' => $orcamento->valor,
            'valor_solicitado' => $validated['valor_solicitado'] ?? null,
            'mensagem_solicitacao' => $validated['descricao'],
            'criado_por' => Auth::id(),
            'status' => 'pendente',
        ]);

        // Notifica o prestador se a configuração estiver ativa
        $shouldNotify = NotificationSetting::where('key', 'notify_negotiation')->first()?->value === '1';
        if ($shouldNotify && $orcamento->prestador) {
            $url = route('prestador.link.show', $orcamento->link_prestador_id ? $orcamento->linkPrestador->token : ($orcamento->linkPublico ? $orcamento->linkPublico->token : ''));
            $orcamento->prestador->notify(new NegotiationRequestedNotification($orcamento, $negociacao, $url));
        }

        return redirect()->back()->with('success', 'Solicitação de negociação enviada!');
    }

    /**
     * Gera um link público para compartilhar a demanda
     */
    public function gerarLink(Request $request, Demanda $demanda)
    {
        $this->authorize('view', $demanda);

        // Valida WhatsApp e nome opcional do prestador
        $validated = $request->validate([
            'whatsapp' => 'required|string|min:10',
            'nome_prestador' => 'nullable|string|max:255',
        ]);

        // Limpa o WhatsApp para salvar apenas números
        $whatsappLimpo = preg_replace('/\D/', '', $validated['whatsapp']);

        // Verifica se já existe um link ativo para este WhatsApp nesta demanda
        $linkExistente = LinkDemandaPublico::where('demanda_id', $demanda->id)
            ->where('whatsapp', $whatsappLimpo)
            ->where('ativo', true)
            ->first();

        if ($linkExistente) {
            return redirect()->back()
                ->with('info', 'Já existe um link ativo enviado para este WhatsApp.');
        }

        // Cria novo link (expira em 30 dias por padrão)
        // Não exige mais CPF/CNPJ e Token de Acesso obrigatórios no início
        $link = LinkDemandaPublico::create([
            'demanda_id' => $demanda->id,
            'administradora_id' => $demanda->administradora_id,
            'token' => LinkDemandaPublico::gerarToken(),
            'whatsapp' => $whatsappLimpo,
            'nome_prestador' => $validated['nome_prestador'],
            'token_gerado_em' => now(),
            'ativo' => true,
            'expira_em' => now()->addDays(30),
        ]);

        // Notifica via WhatsApp se a configuração estiver ativa (On-demand routing)
        $shouldNotify = NotificationSetting::where('key', 'notify_new_demand')->first()?->value === '1';
        if ($shouldNotify) {
            Notification::route(WhatsAppChannel::class, $whatsappLimpo)
                ->notify(new NewDemandNotification($demanda, $link, $validated['nome_prestador']));
        }

        return redirect()->back()->with('success', 'Link público gerado com sucesso para o WhatsApp ' . $validated['whatsapp']);
    }

    /**
     * Desativa um link público da demanda
     */
    public function desativarLink(Demanda $demanda, $linkId)
    {
        $this->authorize('view', $demanda);

        $link = LinkDemandaPublico::where('demanda_id', $demanda->id)
            ->findOrFail($linkId);
        
        $link->update(['ativo' => false]);

        return redirect()->back()->with('success', 'Link desativado com sucesso!');
    }
}
