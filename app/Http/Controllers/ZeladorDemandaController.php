<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\DemandaAnexo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ZeladorDemandaController extends Controller
{
    /**
     * Lista todas as demandas do condomínio do zelador
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isZelador() || !$user->condominio_id) {
            abort(403, 'Acesso negado.');
        }

        $condominio = $user->condominio;
        
        $demandas = $condominio->demandas()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('zelador.demandas.index', compact('demandas', 'condominio'));
    }

    /**
     * Exibe formulário para criar nova demanda
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isZelador() || !$user->condominio_id) {
            abort(403, 'Acesso negado.');
        }

        $condominio = $user->condominio;

        return view('zelador.demandas.create', compact('condominio'));
    }

    /**
     * Salva nova demanda
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isZelador() || !$user->condominio_id) {
            abort(403, 'Acesso negado.');
        }

        $condominio = $user->condominio;

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'urgencia' => 'nullable|in:baixa,media,alta,critica',
            'anexos.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,pdf|max:10240', // 10MB por arquivo
        ]);

        $demanda = Demanda::create([
            'empresa_id' => $user->empresa_id,
            'condominio_id' => $condominio->id,
            'usuario_id' => $user->id,
            'titulo' => (string) $validated['titulo'],
            'descricao' => (string) $validated['descricao'],
            'urgencia' => $validated['urgencia'] ?? null,
            'status' => 'aberta',
        ]);

        // Processa anexos (fotos)
        if ($request->hasFile('anexos')) {
            foreach ($request->file('anexos') as $arquivo) {
                $nomeArquivo = Str::uuid() . '.' . $arquivo->getClientOriginalExtension();
                $caminho = $arquivo->storeAs('demandas/anexos', $nomeArquivo, 'public');

                DemandaAnexo::create([
                    'demanda_id' => $demanda->id,
                    'empresa_id' => $user->empresa_id,
                    'nome_original' => $arquivo->getClientOriginalName(),
                    'nome_arquivo' => $nomeArquivo,
                    'caminho' => $caminho,
                    'mime_type' => $arquivo->getMimeType(),
                    'tamanho' => $arquivo->getSize(),
                ]);
            }
        }

        return redirect()->route('zelador.demandas.index')
            ->with('success', 'Demanda criada com sucesso!');
    }

    /**
     * Exibe detalhes de uma demanda
     */
    public function show(Demanda $demanda)
    {
        $user = Auth::user();
        
        if (!$user->isZelador() || !$user->condominio_id) {
            abort(403, 'Acesso negado.');
        }

        // Verifica se a demanda pertence ao condomínio do zelador
        if ($demanda->condominio_id !== $user->condominio_id) {
            abort(403, 'Acesso negado. Esta demanda não pertence ao seu condomínio.');
        }

        $demanda->load(['orcamentos.prestador', 'orcamentos.negociacoes', 'anexos']);

        return view('zelador.demandas.show', compact('demanda'));
    }
}
