<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function show(Request $request, $slug)
    {

        $days = isset($request->days) ? $request->days : 1;

        $chart = \Http::get('https://api.coingecko.com/api/v3/coins/'.$slug.'/market_chart?vs_currency=eur&days='.$days);

        $chart = json_decode($chart, 1);

        return [
            'prices' => collect($chart['prices'])->transform(function($item) {
                $item[0] = date('d M Y H:i:s Z', $item[0]/1000);
                return $item;
            }),
            'market_caps' => collect($chart['market_caps'])->transform(function($item) {
                $item[0] = date('d M Y H:i:s Z', $item[0]/1000);
                return $item;
            }),
            'total_volumes' => collect($chart['total_volumes'])->transform(function($item) {
                $item[0] = date('d M Y H:i:s Z', $item[0]/1000);
                return $item;
            })
        ];

    }
}
