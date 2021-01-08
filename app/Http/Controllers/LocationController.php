<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;

class LocationController extends Controller
{

    private $viacep_api = 'https://viacep.com.br/ws/';

    function zipcode(Request $request, string $zipcode) {
        
        //Montando url completa
        $url = $this->viacep_api.$zipcode.'/json/unicode';

        //cache

        //Requisitando dados
        $response = Http::get($url);

        //Retorna resposta
        return $response->json();

    }

}
