<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CryptoController extends Controller
{

    var $keyApi = 'ae1c3a64-2b7e-4aa3-80a1-bf5f4a6e85c6';


    /*public function getAllCryptoPrices(){
        return "Prueba hola";
    }*/

    //Método para consultar la informacipn de los precios desde el api
    public function getAllCryptoPrices()
    {
        $apiKey = $this->keyApi;
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->get($url, [
            'start' => 1,
            'limit' => 100,
            'convert' => 'USD',
        ]);

        //Valida si la respuesta tuvo exito o no
        if ($response->successful()) {
            $data = $response->json();
            $cryptos = $data['data'];

            //Extraemos la información a mostrar
            $cryptoData = array_map(function ($crypto) {
                return [
                    'name' => $crypto['name'],
                    'symbol' => $crypto['symbol'],
                    'price_usd' => $crypto['quote']['USD']['price'],
                    'percent_change_24h' => $crypto['quote']['USD']['percent_change_24h'],
                    'volume_24h' => $crypto['quote']['USD']['volume_24h'],
                ];
            }, $cryptos);

            $entry = [
                'timestamp' => now()->toDateTimeString(),
                'data' => $cryptoData
            ];

            //Ruta del archivo en storage/app/crypto_history.json
            $filePath = 'crypto_history.json';

            //Lee el archivo json 
            $existingData = [];
            if (Storage::exists($filePath)) {
                $existingData = json_decode(Storage::get($filePath), true);
            }

            // Agregar el nuevo registro
            $existingData[] = $entry;

            //Actualiza o guarda el archivo con los nuevops datos
            Storage::put($filePath, json_encode($existingData, JSON_PRETTY_PRINT));



            //Esta parte no estaba dentro de los requerimientos pero quise agregarla, esto es para auditar cada vez que se haga una petición 
            $logData = [
                'fecha' => now()->toDateTimeString(),
                'ip' => request()->ip(),
                'endpoint' => request()->path(),
                'metodo' => request()->method(),
                'cantidad_monedas' => count($cryptoData),
            ];

            $logLine = json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $logFile = 'logs/api_log_' . now()->format('Y-m-d') . '.txt';


            Storage::append($logFile, $logLine);
            return response()->json($cryptoData);
        } else {
            return response()->json(['error' => 'Error al obtener los datos.'], 500);
        }
    }


    //Método para obtener la infromación de una criptomneda en especifico
    public function getCryptoInfo($symbol)
    {
        $apiKey = $this->keyApi;
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/info';

        $response = Http::withHeaders([
            'X-CMC_PRO_API_KEY' => $apiKey,
        ])->get($url, [
            'symbol' => strtoupper($symbol),
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (!isset($data['data'][strtoupper($symbol)])) {
                return response()->json(['error' => 'Símbolo no encontrado.'], 404);
            }

            $info = $data['data'][strtoupper($symbol)];

            $cryptoInfo = [
                'name' => $info['name'],
                'symbol' => $info['symbol'],
                'logo' => $info['logo'],
                'category' => $info['category'],
                'description' => $info['description'],
                'date_added' => $info['date_added'],
                'website' => $info['urls']['website'][0] ?? null,
                'whitepaper' => $info['urls']['technical_doc'][0] ?? null,
            ];

            //Esta parte no estaba dentro de los requerimientos pero quise agregarla, esto es para auditar cada vez que se haga una petición 
            $logData = [
                'fecha' => now()->toDateTimeString(),
                'ip' => request()->ip(),
                'endpoint' => request()->path(),
                'metodo' => request()->method(),
                'cantidad_monedas' => count($cryptoInfo),
            ];

            $logLine = json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $logFile = 'logs/api_log_' . now()->format('Y-m-d') . '.txt';

            Storage::append($logFile, $logLine);

            return response()->json($cryptoInfo);
        } else {
            return response()->json(['error' => 'Error al obtener la información de la criptomoneda.'], 500);
        }
    }
}
