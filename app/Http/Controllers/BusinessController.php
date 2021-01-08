<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Business as BusinessModel;

class BusinessController extends Controller
{

    function get(Request $request) {
        
        //Retorna dados do token solicitado
        $supports = BusinessModel::select(['name','site', 'logo'])->groupBy(['name', 'site', 'logo'])->get();

        //Retorna resposta
        return response()->json($supports);

    }

}
