<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\CondominioController;
use App\Http\Controllers\PrestadorController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\LinkPrestadorController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CondominioPublicoController;
use App\Http\Controllers\PrestadorPublicoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    
    // Redireciona zeladores para área específica
    if ($user->isZelador()) {
        return redirect()->route('zelador.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile - disponível para todos os usuários autenticados
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserBelongsToEmpresa::class, \App\Http\Middleware\EnsureUserIsNotZelador::class])->group(function () {

    // Empresas (apenas admin)
    Route::resource('empresas', EmpresaController::class)->middleware('can:admin');

    // Condomínios
    Route::resource('condominios', CondominioController::class);
    Route::post('condominios/{condominio}/links', [CondominioController::class, 'gerarLink'])->name('condominios.gerar-link');
    Route::get('condominios/{condominio}/links', [CondominioController::class, 'links'])->name('condominios.links');
    Route::post('condominios/{condominio}/links/{link}/desativar', [CondominioController::class, 'desativarLink'])->name('condominios.desativar-link');

    // Prestadores
    Route::resource('prestadores', PrestadorController::class);

    // Demandas
    Route::resource('demandas', DemandaController::class);
    Route::post('demandas/{demanda}/status', [DemandaController::class, 'updateStatus'])->name('demandas.update-status');
    Route::post('demandas/{demanda}/prestadores', [DemandaController::class, 'adicionarPrestadores'])->name('demandas.adicionar-prestadores');
    Route::delete('demandas/{demanda}/prestadores/{prestador}', [DemandaController::class, 'removerPrestador'])->name('demandas.remover-prestador');
    Route::post('demandas/{demanda}/orcamentos/{orcamento}/aprovar', [DemandaController::class, 'aprovarOrcamento'])->name('demandas.aprovar-orcamento');
    Route::post('demandas/{demanda}/orcamentos/{orcamento}/rejeitar', [DemandaController::class, 'rejeitarOrcamento'])->name('demandas.rejeitar-orcamento');
    Route::post('demandas/{demanda}/orcamentos/{orcamento}/negociacao', [DemandaController::class, 'criarNegociacao'])->name('demandas.criar-negociacao');

    // Orçamentos
    Route::resource('orcamentos', OrcamentoController::class);
    Route::post('orcamentos/{orcamento}/aprovar', [OrcamentoController::class, 'aprovar'])->name('orcamentos.aprovar');
    Route::post('orcamentos/{orcamento}/rejeitar', [OrcamentoController::class, 'rejeitar'])->name('orcamentos.rejeitar');

    // Documentos
    Route::resource('documentos', DocumentoController::class);
    Route::get('documentos/{documento}/visualizar', [DocumentoController::class, 'visualizar'])->name('documentos.visualizar');
    Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');

    // Tags
    Route::resource('tags', TagController::class);
});

// Rotas para Zeladores
Route::middleware(['auth', 'verified'])->prefix('zelador')->name('zelador.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ZeladorController::class, 'dashboard'])->name('dashboard');
    Route::resource('demandas', \App\Http\Controllers\ZeladorDemandaController::class)->except(['edit', 'update', 'destroy']);
});

// Rotas públicas para prestadores (sem autenticação)
Route::get('/prestador/{token}', [LinkPrestadorController::class, 'show'])->name('prestador.link.show');
Route::post('/prestador/{token}/orcamento', [LinkPrestadorController::class, 'enviarOrcamento'])->name('prestador.link.orcamento');
Route::post('/prestador/{token}/negociacoes/{negociacao}/aceitar', [LinkPrestadorController::class, 'aceitarNegociacao'])->name('prestador.link.aceitar-negociacao');
Route::post('/prestador/{token}/negociacoes/{negociacao}/recusar', [LinkPrestadorController::class, 'recusarNegociacao'])->name('prestador.link.recusar-negociacao');

// Rotas públicas para criação de demandas e cadastro de prestadores
Route::get('/publico/demanda/{token}', [CondominioPublicoController::class, 'criarDemanda'])->name('publico.criar-demanda');
Route::post('/publico/demanda/{token}', [CondominioPublicoController::class, 'storeDemanda'])->name('publico.store-demanda');
Route::get('/publico/prestador/cadastro', [PrestadorPublicoController::class, 'cadastro'])->name('publico.cadastro-prestador');
Route::post('/publico/prestador/cadastro', [PrestadorPublicoController::class, 'store'])->name('publico.store-prestador');

// API para busca de CNPJ
Route::get('/api/buscar-cnpj', [\App\Http\Controllers\ApiController::class, 'buscarCNPJ'])->name('api.buscar-cnpj');

require __DIR__.'/auth.php';
