<?php

use App\Http\Controllers\{
    SolicitudController,
    AuthController,
    ColaboradoresPeruController,
    ConfiguracionController,
    SolicitudColaboradorController,
    TerminosController
};
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('auth.login.initial');

//LOGIN
Route::get('login/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('login.google.temp');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    //SOLICITUD
    Route::get('redirectpage/solicitud', [SolicitudController::class, 'redirectPage'])->name('redirect.solicitud');
    Route::get('list/solicitud', [SolicitudController::class, 'listAll'])->name('list.solicitud');
    Route::post('create/solicitud', [SolicitudController::class, 'create'])->name('create.solicitud');
    
    //SOLICITUD COLABORADOR
    Route::put('solicitudes/status', [SolicitudColaboradorController::class, 'updateStatus'])->name('solicitud.colaborador.update.status');
    Route::put('solicitudes/status/masive', [SolicitudColaboradorController::class, 'updateAllStatus'])->name('solicitud.colaborador.update.masive');
    
    //CONFIGURACION AREA
    Route::get('redirectpage/configuraciones/areas', [ConfiguracionController::class, 'listAllArea'])->name('configuracion.list.area');
    
    //COLABORADORES PERU
    Route::get('redirectpage/colaboradores/pe', [ColaboradoresPeruController::class, 'redirectPage'])->name('redirect.colaboradores.pe');
    Route::get('colaboradores/pe', [ColaboradoresPeruController::class, 'getColaboradoresPe'])->name('list.colaboradores.pe');
    
    //TERMINOS PERU
    Route::get('terminos/list', [TerminosController::class, 'listAll'])->name('terminos.list');
});    

