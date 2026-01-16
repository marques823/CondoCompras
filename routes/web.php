<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\CondominioController;
use App\Http\Controllers\PrestadorController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\LinkPrestadorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureUserBelongsToEmpresa::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Empresas (apenas admin)
    Route::resource('empresas', EmpresaController::class)->middleware('can:admin');

    // Condomínios
    Route::resource('condominios', CondominioController::class);

    // Prestadores
    Route::resource('prestadores', PrestadorController::class);

    // Demandas
    Route::resource('demandas', DemandaController::class);
    Route::post('demandas/{demanda}/gerar-links', [DemandaController::class, 'gerarLinks'])->name('demandas.gerar-links');

    // Orçamentos
    Route::resource('orcamentos', OrcamentoController::class);
    Route::post('orcamentos/{orcamento}/aprovar', [OrcamentoController::class, 'aprovar'])->name('orcamentos.aprovar');
    Route::post('orcamentos/{orcamento}/rejeitar', [OrcamentoController::class, 'rejeitar'])->name('orcamentos.rejeitar');

    // Documentos
    Route::resource('documentos', DocumentoController::class);
});

// Rotas públicas para prestadores (sem autenticação)
Route::get('/prestador/{token}', [LinkPrestadorController::class, 'show'])->name('prestador.link.show');
Route::post('/prestador/{token}/orcamento', [LinkPrestadorController::class, 'enviarOrcamento'])->name('prestador.link.orcamento');

// API para busca de CNPJ
Route::get('/api/buscar-cnpj', [\App\Http\Controllers\ApiController::class, 'buscarCNPJ'])->name('api.buscar-cnpj');

require __DIR__.'/auth.php';
