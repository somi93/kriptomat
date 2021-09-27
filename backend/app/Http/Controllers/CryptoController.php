<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

class CryptoController extends Controller
{
    private $_client;

    public function __construct()
    {
        $this->_client = new CoinGeckoClient();
    }

    public function serverStatus()
    {

        return $data = $this->_client->ping();
    }

}
