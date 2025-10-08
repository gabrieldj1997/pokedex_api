<?php

use App\Http\Controllers\PokemonController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('pokemon');
});

Route::group(['prefix' => 'pokemon'], function () {
    Route::get('/', [PokemonController::class, 'pokemonList'])->name('getPokemonList');
    Route::get('/get/{identifier}', [PokemonController::class, 'getPokemonByIdOrName'])->name('getPokemonByIdOrName');
    Route::get('/sprites', [PokemonController::class, 'getImages'])->name('getPokemonSprite');
    Route::get('/discovered/{id}', [PokemonController::class, 'discoveredPokemon'])->name('discoveredPokemon');
    Route::get('/challenge', [PokemonController::class, 'challengePokemon'])->name('challengePokemon');
    Route::get('/reset/list', [PokemonController::class, 'resetList'])->name('resetList');
    Route::get('/reset/img', [PokemonController::class, 'resetSprites'])->name('resetSprites');
});

require __DIR__ . '/auth.php';
