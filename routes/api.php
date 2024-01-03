<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColaboradoresPeruController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
});
Route::get('colape', [ColaboradoresPeruController::class, 'validateLideresPE'])->name('colape');
Route::get('test', [SolicitudController::class, 'listAll'])->name('test');
Route::get('validateLideresPE', [AuthController::class, 'validateLideresPE'])->name('validateLideresPE');
