<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;
use App\Http\Controllers\HomeController;

Route::get('/', HomeController::class);

Route::get('getPrice', [CryptoController::class, 'getAllCryptoPrices']);
Route::get('getInfo/{symbol}', [CryptoController::class, 'getCryptoInfo']);





