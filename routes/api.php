<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AsignacionSeguimientoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\TareaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class,'login'])->name('login');

Route::group(['middleware' => ['auth:api']], function () {


    //Rutas de Tareas
    Route::resource('tarea/{id}',TareaController::class)->only(['index','store']);
    Route::resource('operaciones/tarea',TareaController::class)->only(['update','show','destroy']);
    Route::post('tarea/subir_imagen',[TareaController::class,'subirArchivos']);

    //Rutas de Proyecto
    Route::resource('proyecto',ProyectoController::class)->only(['index','store','update','show','destroy']);

    //Rutas de Login
    Route::post('logout', [AuthController::class,'logout'])->name('logout');
    Route::get('users', [AuthController::class,'index'])->name('users.index');
    Route::get('users/{user}', [AuthController::class,'show'])->name('users.show');
    Route::post('users/store', [AuthController::class,'store'])->name('users.store');
    Route::put('users/update/{user}', [AuthController::class,'update'])->name('users.update');

    //rutas de calendario
    Route::put('estado', [AsignacionSeguimientoController::class,'actualizarEstado']);
    Route::get('comentarios/{id}', [ComentarioController::class,'index']);

    //rutas de calendario
    Route::get('metricas', [AsignacionSeguimientoController::class,'metricas']);
});

