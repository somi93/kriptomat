<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarketResource;
use App\Models\Coin;
use Illuminate\Http\Request;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

class MarketController extends Controller
{
    private $_client;

    public function __construct()
    {
        $this->_client = new CoinGeckoClient();
    }

    public function index(Request $request)
    {
        $currency = isset($request->currency) ? $request->currency : 'usd';
        $per_page = isset($request->per_page) ? $request->per_page : 10;
        $page = isset($request->page) ? $request->page : 1;

        $coins = $this->_client->coins()->getMarkets($currency, [
            'per_page' => $per_page,
            'page' => $page
        ]);

        $coins = collect($coins)->map(function($coin) {
            $coin['chart'] = \Http::get('https://api.coingecko.com/api/v3/coins/'.$coin['id'].'/market_chart?vs_currency=eur&days=7&interval=daily');

            return $coin;
        });

        return MarketResource::collection($coins);
    }

    public function syncMarket(Request  $request)
    {
        $data = $this->_client->coins()->getList();

        $data = collect($data)->map(function($coin) {
           $coin['coin_id'] = $coin['id'];
           unset($coin['id']);
           return $coin;
        });

        $check = Coin::count();

        if($check === 0) {
            try {

                collect($data)->each(function($coin){
                    Coin::create($coin);
                });

                return response()->json([
                    'state' => 'Success'
                ], 201);
            }
            catch (\Error $e) {

                return response()->json([
                    'state' => 'Failed'
                ], 400);

            }
        }
        else {
            return response()->json([
                'state' => 'All is synced'
            ], 201);
        }

    }
}
