<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Coin;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

class CoinController extends Controller
{
    private $_client;

    public function __construct()
    {
        $this->_client = new CoinGeckoClient();
    }

    public function show(Request $request, $slug)
    {
        $tickers     = $request->has('tickers') ? true : false;
        $market_data = $request->has('market_data') ? true : false;

        $coin = $this->_client->coins()->getCoin($slug, ['tickers' => $tickers, 'market_data' => $market_data]);

        //$chart = \Http::get('https://api.coingecko.com/api/v3/coins/'.$slug.'/market_chart?vs_currency=eur&days=7&interval=daily');
        //$coin['chart'] = json_decode($chart, 1);

        return $coin;
    }

    public function favoriteStore(Request $request)
    {

        $fav = Favorite::create($request->toArray());

        if($fav) {
            return response()->json([
                'data' => $fav,
                'state' => 'Success'
            ], 201);
        }

        return response()->json(['state' => 'Failed'], 400);
    }

    public function alertStore(Request $request)
    {

        $alert = Alert::create($request->toArray());

        if($alert) {
            return response()->json([
                'data' => $alert,
                'state' => 'Success'
            ], 201);
        }

        return response()->json(['state' => 'Failed'], 400);
    }

    public function allFavorites(Request $request)
    {
        $favs = Favorite::where('state', 1)
            ->where('user_id', $request->user_id)
            ->get();

        return collect($favs)->transform(function($fav) use ($request) {
            $coin = $this->show($request, $fav['name']);
            $fav['symbol'] = $coin['symbol'];
            $fav['image'] = $coin['image'];
            return $fav;
        });
    }

    public function allAlerts(Request $request)
    {
        $alerts = Alert::where('state', 1)
            ->where('user_id', $request->user_id)
            ->get();

        return collect($alerts)->transform(function($alert) use ($request) {
            $coin = $this->show($request, $alert['name']);
            $alert['symbol'] = $coin['symbol'];
            $alert['image'] = $coin['image'];
            return $alert;
        });
    }

    public function favoriteEdit(Request $request, $id)
    {

        try {
            Favorite::find($id)->update($request->toArray());

            return response()->json([
                'data' => Favorite::find($id),
                'state' => 'Success'
            ], 201);
        }
        catch (\Error $e) {
            return response()->json(['state' => 'Failed'], 400);
        }


    }

    public function alertEdit(Request $request, $id)
    {

        try {
            Alert::find($id)->update($request->toArray());

            return response()->json([
                'data' => Alert::find($id),
                'state' => 'Success'
            ], 201);
        }
        catch (\Error $e) {
            return response()->json(['state' => 'Failed'], 400);
        }
    }

    public function find(Request $request)
    {
        return Coin::where('name', 'LIKE', $request->q.'%')->get();
    }
}
