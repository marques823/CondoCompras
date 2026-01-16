<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemandaApiController;
use App\Http\Controllers\Api\PrestadorApiController;
use App\Http\Controllers\Api\OrcamentoApiController;
use App\Http\Controllers\Api\DocumentoApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas públicas (para links de prestadores)
Route::get('/prestador/link/{token}', [\App\Http\Controllers\LinkPrestadorController::class, 'show'])
    ->name('api.prestador.link.show');

Route::post('/prestador/link/{token}/orcamento', [\App\Http\Controllers\LinkPrestadorController::class, 'enviarOrcamento'])
    ->name('api.prestador.link.orcamento');

// Rotas protegidas por token (para n8n e integrações)
Route::middleware(['api', 'auth:sanctum'])->group(function () {
    
    // Demandas
    Route::apiResource('demandas', DemandaApiController::class);
    
    // Prestadores
    Route::get('prestadores', [PrestadorApiController::class, 'index'])->name('api.prestadores.index');
    Route::get('prestadores/{id}', [PrestadorApiController::class, 'show'])->name('api.prestadores.show');
    
    // Orçamentos
    Route::apiResource('orcamentos', OrcamentoApiController::class);
    Route::post('orcamentos/{id}/aprovar', [OrcamentoApiController::class, 'aprovar'])->name('api.orcamentos.aprovar');
    Route::post('orcamentos/{id}/rejeitar', [OrcamentoApiController::class, 'rejeitar'])->name('api.orcamentos.rejeitar');
    
    // Documentos
    Route::post('documentos', [DocumentoApiController::class, 'store'])->name('api.documentos.store');
    Route::get('documentos', [DocumentoApiController::class, 'index'])->name('api.documentos.index');
    Route::get('documentos/{id}', [DocumentoApiController::class, 'show'])->name('api.documentos.show');
});
