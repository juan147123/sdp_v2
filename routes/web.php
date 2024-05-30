<?php

use App\Http\Controllers\{
    SolicitudController,
    AuthController,
    ColaboradoresChileController,
    ColaboradoresPeruController,
    ConfiguracionController,
    SolicitudColaboradorController,
    TerminosController,
    UsuarioController
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
    Route::post('create/solicitud/multiple', [SolicitudController::class, 'createMultiple'])->name('create.solicitud.multiple');
    
    //SOLICITUD APROBAR CC 
    Route::get('redirectpage/solicitud/aprobar', [SolicitudController::class, 'redirectPageSolicitudObraAprobarCC'])->name('redirect.solicitud.aprobar');
    Route::get('list/solicitud/aprobar', [SolicitudController::class, 'listAllCCAprobar'])->name('list.solicitud.aprobar');
    //APROBADOR CC ESTADO
    Route::put('solicitudes/status/cc', [SolicitudColaboradorController::class, 'updateStatusAprobadorCC'])->name('solicitud.colaborador.update.status.cc');
    Route::put('solicitudes/status/masive/cc', [SolicitudColaboradorController::class, 'updateAllStatusAprobadorCC'])->name('solicitud.colaborador.update.masive.cc');

    //SOLICITUD OBRA
    Route::get('redirectpage/solicitud/obra', [SolicitudController::class, 'redirectPageSolicitudObra'])->name('redirect.solicitud.obra');

    
    //SOLICITUD COLABORADOR
    Route::put('solicitudes/status', [SolicitudColaboradorController::class, 'updateStatus'])->name('solicitud.colaborador.update.status');
    Route::put('solicitudes/status/masive', [SolicitudColaboradorController::class, 'updateAllStatus'])->name('solicitud.colaborador.update.masive');
    
    //CONFIGURACION AREA
    Route::get('redirectpage/configuraciones/areas', [ConfiguracionController::class, 'listAllArea'])->name('configuracion.list.area');
    
    //COLABORADORES PERU
    Route::get('redirectpage/colaboradores/pe', [ColaboradoresPeruController::class, 'redirectPage'])->name('redirect.colaboradores.pe');
    Route::get('colaboradores/pe', [ColaboradoresPeruController::class, 'getColaboradoresPe'])->name('list.colaboradores.pe');
    
    //COLABORADORES CHILE
    Route::get('redirectpage/colaboradores/cl', [ColaboradoresChileController::class, 'redirectPage'])->name('redirect.colaboradores.cl');
    Route::get('colaboradores/cl', [ColaboradoresChileController::class, 'getColaboradoresCl'])->name('list.colaboradores.cl');
    
    //COLABORADORES CHILE | OBRA
    Route::get('redirectpage/obra/colaboradores/cl', [ColaboradoresChileController::class, 'redirectPageObraCl'])->name('redirect.colaboradores.obra.cl');
    Route::get('colaboradores/obra/cl', [ColaboradoresChileController::class, 'getColaboradoresObra'])->name('list.colaboradores.obra.cl');
    
    //TERMINOS PERU
    Route::get('terminos/list', [TerminosController::class, 'listAll'])->name('terminos.list');
    
    
    // configuraciones / áreas / checklist
    Route::get('configuraciones/areas', [ConfiguracionController::class, 'listAll'])->name('redirect.configuraciones');
    Route::post('configuraciones/areas ', [ConfiguracionController::class, 'create'])->name('configuraciones.post');
    Route::put('configuraciones/areas ', [ConfiguracionController::class, 'update'])->name('configuraciones.update');
    Route::put('configuraciones/areas/delete ', [ConfiguracionController::class, 'delete'])->name('configuraciones.delete');
    Route::get('configuraciones/area', [ConfiguracionController::class, 'listByIdArea'])->name('list.configuracion.idarea');
    
    //USUARIOS
    Route::post('usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::put('usuarios/update', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::put('usuarios/delete', [UsuarioController::class, 'delete'])->name('usuarios.delete');
    
    //CKECKLIST AREAS
    Route::get('solicitudes/area', [SolicitudController::class, 'redirectByArea'])->name('redirect.solicitud.area');
    Route::get('list/solicitudes/area', [SolicitudController::class, 'listSolicitudesByArea'])->name('list.solicitud.area');
});
