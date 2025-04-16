<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LogController;


Route::group(['middleware' => ['auth:sanctum', 'refreshTokenSanctum','log.access']], function () {
   
    //logs
    Route::get('logs/formatted',[LogController::class,'getFormattedLogs']);
    Route::get('logs/search',[LogController::class,'search']);

                                // -----Exemplo de uso-----
    // /*
    // |--------------------------------------------------------------------------
    // | Profiles Routes
    // |--------------------------------------------------------------------------
    // */
    // Route::prefix('profiles')->group(function () {
    //     Route::get('/', [ProfileController::class,'index'])->middleware(['abilities:list_perfil']);
    //     Route::get('/{id}', [ProfileController::class,'show'])->middleware(['abilities:list_perfil']);
    //     Route::get('/{id}/abilities', [ProfileController::class,'getAbilities'])->middleware(['abilities:list_perfil']);
    //     Route::post('/', [ProfileController::class,'beforeStore'])->middleware(['abilities:cad_perfil']);
    //     Route::post('/{id}/abilities', [ProfileController::class,'storeAbilities'])->middleware(['abilities:cad_perfil']);
    //     Route::put('/{id}', [ProfileController::class,'beforeUpdate'])->middleware(['abilities:cad_perfil']);
    //     Route::delete('/{id}', [ProfileController::class,'destroy']);
    // });
    // /*
    // |--------------------------------------------------------------------------
    // | Abilities Routes
    // |--------------------------------------------------------------------------
    // */
    // Route::prefix('abilities')->group(function () {
    //     Route::get('/', [AbilityController::class,'index'])->middleware(['abilities:list_habilidade']);
    //     Route::get('/{id}', [AbilityController::class,'show'])->middleware(['abilities:list_habilidade']);
    //     Route::post('/', [AbilityController::class,'beforeStore'])->middleware(['abilities:cad_habilidade']);
    //     Route::put('/{id}', [AbilityController::class,'beforeUpdate'])->middleware(['abilities:cad_habilidade']);
    //     Route::delete('/{id}', [AbilityController::class,'destroy'])->middleware(['abilities:del_habilidade']);
    // });
    // /*
    // |--------------------------------------------------------------------------
    // | Users Routes
    // |--------------------------------------------------------------------------
    // */
    // Route::prefix('users')->group(function () {
    //     Route::get('/', [UserController::class,'index'])->middleware(['abilities:list_usuario']);
    //     Route::get('/{id}', [UserController::class,'show'])->middleware(['abilities:list_usuario']);
    //     Route::post('/', [UserController::class,'beforeStore'])->middleware(['abilities:cad_usuario']);
    //     Route::put('/{id}', [UserController::class,'beforeUpdate'])->middleware(['abilities:cad_usuario']);
    //     Route::delete('/{id}', [UserController::class,'destroy'])->middleware(['abilities:del_usuario']);
    //     Route::put('/restore/{id}', [UserController::class,'restore'])->middleware(['abilities:del_usuario']);
    // });

    
});
