<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Symfony\Component\HttpFoundation\Cookie;

class TokenController extends Controller
{

    function register(Request $request) {
        
        //Gerando token único
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        //Setando cookie na sessão
        $cookie = $this->createCookie($token);      

        //Retorna resposta
        return response(['success' => ['token' => $token ]])->withCookie($cookie);
    }

    function get(Request $request) {
        
        //Retorna dados do token solicitado
        $token = $request->cookie(env('COOKIE_NAME'));      

        //Atribui resposta booleana
        $response = (is_null($token))? false : $token;

        //Retorna resposta
        return response($response);

    }

    function post(Request $request) {
        
        //Dados não preenchidos
        if (!$request->has('token') || !$request->filled('token') ) return false;

        //Token enviado
        $token = filter_var($request->input('token'), FILTER_SANITIZE_STRING);

        //Setando cookie na sessão
        $cookie = $this->createCookie($token);      

        //Retorna resposta
        return response(['success' => ['token' => $token ]])->withCookie($cookie);

    }

    /** Criar cookie */
    private function createCookie(string $token) {
        //Setando cookie na sessão
        return Cookie::create(env('COOKIE_NAME'), $token, 0, '/', env('COOKIE_DOMAIN'), env('COOKIE_SECURE'), true);    
    }

}
