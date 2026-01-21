<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdministradoraController;
use App\Http\Controllers\CondominioController;
use App\Http\Controllers\PrestadorController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\OrcamentoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\LinkPrestadorController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CondominioPublicoController;
use App\Http\Controllers\DemandaPublicaController;
use App\Http\Controllers\PrestadorPublicoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GerenteController;
use App\Http\Controllers\ZeladorController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified', 'context'])->group(function () {
    
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) return view('dashboard');
        if ($user->isAdministradora()) return redirect()->route('administradora.dashboard');
        if ($user->isGerente()) return redirect()->route('gerente.dashboard');
        if ($user->isZelador()) return redirect()->route('zelador.dashboard');
        return view('dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- ÁREA ADMIN (SUPER ADMIN) ---
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('administradoras', AdministradoraController::class);
        Route::get('administradoras/{administradora}/confirm-destroy', [AdministradoraController::class, 'confirmDestroy'])->name('administradoras.confirm-destroy');
    });

    // --- ÁREA ADMINISTRADORA / GERENTE (COMPARTILHADA) ---
    // O controle de QUEM pode criar/editar/deletar é feito via POLICIES nos Controllers
    Route::middleware(['role:administradora,gerente'])->group(function () {
        Route::resource('condominios', CondominioController::class);
        Route::post('condominios/{condominio}/gerar-link', [CondominioController::class, 'gerarLink'])->name('condominios.gerar-link');
        Route::post('condominios/{condominio}/links/{link}/desativar', [CondominioController::class, 'desativarLink'])->name('condominios.desativar-link');
        
        Route::resource('prestadores', PrestadorController::class);
        Route::resource('tags', TagController::class);
        Route::resource('users', UserController::class);
        
        // Configuração específica da Administradora
        Route::get('/administradora/config', [AdministradoraController::class, 'editConfig'])->name('administradora.config');
        Route::patch('/administradora/config', [AdministradoraController::class, 'updateConfig'])->name('administradora.config.update');
    });

    // --- ÁREA OPERACIONAL (ADMINISTRADORA, GERENTE, ZELADOR) ---
    Route::middleware(['role:administradora,gerente,zelador'])->group(function () {
        Route::resource('demandas', DemandaController::class);
        Route::post('demandas/{demanda}/status', [DemandaController::class, 'updateStatus'])->name('demandas.update-status');
        Route::post('demandas/{demanda}/orcamentos/{orcamento}/aprovar', [DemandaController::class, 'aprovarOrcamento'])->name('demandas.aprovar-orcamento');
        Route::post('demandas/{demanda}/orcamentos/{orcamento}/rejeitar', [DemandaController::class, 'rejeitarOrcamento'])->name('demandas.rejeitar-orcamento');
        Route::post('demandas/{demanda}/orcamentos/{orcamento}/negociar', [DemandaController::class, 'criarNegociacao'])->name('demandas.criar-negociacao');
        Route::post('demandas/{demanda}/prestadores', [DemandaController::class, 'adicionarPrestador'])->name('demandas.adicionar-prestador');
        Route::delete('demandas/{demanda}/prestadores/{prestador}', [DemandaController::class, 'removerPrestador'])->name('demandas.remover-prestador');
        Route::post('demandas/{demanda}/gerar-link', [DemandaController::class, 'gerarLink'])->name('demandas.gerar-link');
        Route::post('demandas/{demanda}/links/{link}/desativar', [DemandaController::class, 'desativarLink'])->name('demandas.desativar-link');
        
        Route::resource('orcamentos', OrcamentoController::class);
        Route::resource('documentos', DocumentoController::class);
        Route::get('documentos/{documento}/visualizar', [DocumentoController::class, 'visualizar'])->name('documentos.visualizar');
        Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    });

    // Dashboards específicos
    Route::get('/administradora/dashboard', [AdministradoraController::class, 'dashboard'])->name('administradora.dashboard')->middleware('role:administradora');
    Route::get('/gerente/dashboard', [GerenteController::class, 'dashboard'])->name('gerente.dashboard')->middleware('role:gerente');
    Route::get('/zelador/dashboard', [ZeladorController::class, 'dashboard'])->name('zelador.dashboard')->middleware('role:zelador');

});

// Rota de logout sempre acessível
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/logout', function () {
    return redirect()->route('login');
})->middleware('auth')->name('logout.get');

// Rotas públicas
// Rotas de login para links de prestadores
Route::get('/prestador/{token}/login', [LinkPrestadorController::class, 'login'])->name('prestador.link.login');
Route::post('/prestador/{token}/login', [LinkPrestadorController::class, 'processarLogin'])->name('prestador.link.login.processar');
Route::get('/prestador/{token}', [LinkPrestadorController::class, 'show'])->name('prestador.link.show');
Route::post('/prestador/{token}/orcamento', [LinkPrestadorController::class, 'enviarOrcamento'])->name('prestador.link.orcamento');

// Rotas de login para links públicos de demanda
Route::get('/publico/demanda-prestador/{token}/login', [DemandaPublicaController::class, 'login'])->name('publico.demanda.login');
Route::post('/publico/demanda-prestador/{token}/login', [DemandaPublicaController::class, 'processarLogin'])->name('publico.demanda.login.processar');
Route::get('/publico/demanda-prestador/{token}', [DemandaPublicaController::class, 'show'])->name('publico.demanda.show');
Route::post('/publico/demanda-prestador/{token}/orcamento', [DemandaPublicaController::class, 'enviarOrcamento'])->name('publico.demanda.orcamento');

Route::get('/publico/demanda/{token}', [CondominioPublicoController::class, 'criarDemanda'])->name('publico.criar-demanda');
Route::post('/publico/demanda/{token}', [CondominioPublicoController::class, 'storeDemanda'])->name('publico.store-demanda');
Route::get('/api/buscar-cnpj', [ApiController::class, 'buscarCNPJ'])->name('api.buscar-cnpj');

require __DIR__.'/auth.php';
