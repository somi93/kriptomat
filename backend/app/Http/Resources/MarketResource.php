<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            "symbol" => $this['symbol'],
            "name" => $this['name'],
            "image" => $this['image'],
            "current_price" => $this['current_price'],
            "market_cap" => $this['market_cap'],
            "market_cap_rank" => $this['market_cap_rank'],
            "fully_diluted_valuation" => $this['fully_diluted_valuation'],
            "total_volume" => $this['total_volume'],
            "high_24h" => $this['high_24h'],
            "low_24h" => $this['low_24h'],
            "price_change_24h" => $this['price_change_24h'],
            "price_change_percentage_24h" => $this['price_change_percentage_24h'],
            "market_cap_change_24h" => $this['market_cap_change_24h'],
            "market_cap_change_percentage_24h" => $this['market_cap_change_percentage_24h'],
            "circulating_supply" => $this['circulating_supply'],
            "total_supply" => $this['total_supply'],
            "max_supply" => $this['max_supply'],
            "chart" => [
                "prices" => collect($this['chart']['prices'])->transform(function($item) {
                    $item[0] = date('d M Y H:i:s Z', $item[0]/1000);
                    return $item;
                })
            ]
        ];
    }
}
