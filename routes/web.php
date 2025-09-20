<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;
use App\Http\Controllers\HomeController;

use Illuminate\Support\Facades\Storage;

Route::get('/', HomeController::class);

Route::get('getPrice', [CryptoController::class, 'getAllCryptoPrices']);
Route::get('getInfo/{symbol}', [CryptoController::class, 'getCryptoInfo']);


Route::get('crypto-history', function () {
    $filePath = 'crypto_history.json';

    if (!Storage::exists($filePath)) {
        return response()->json(['error' => 'Archivo no encontrado.'], 404);
    }

    $data = json_decode(Storage::get($filePath), true);
    return response()->json($data);
});



