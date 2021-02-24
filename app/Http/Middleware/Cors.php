<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        $headers = [
            'Access-Control-Allow-Origin'      => 'https://divulgue-servicos.makingpie.com.br',
            'Vary'                             => 'Origin',
            'Access-Control-Allow-Methods'     => 'HEAD, POST, GET, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Accept, Authorization, Content-Type'
        ];

        if ($request->isMethod('OPTIONS')) {
            return response('OK', 200, $headers);
        }

        return $next($request)->withHeaders($headers);
    }
}