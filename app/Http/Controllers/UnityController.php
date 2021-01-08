<?php

namespace App\Http\Controllers;

use App\Unity as UnityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnityController extends Controller
{

    protected static $max = 5;

    function get(Request $request) {
        $unity = UnityModel::where("user_id", "=", Auth::user()->id)->limit(self::$max)->orderBy('ID', 'DESC')->get()->toArray();
        return response()->json($unity);
    }

    function add(Request $request) {

        //Verificar quantidades de unidades por usuário
        if (UnityModel::where("user_id", "=", Auth::user()->id)->count() >= self::$max) 
            return ['error' => ['unity' => 'Você atingiu seu limite máximo de unidades!']];

        //Campos obrigatórios
        $fields = ['name', 'address', 'number', 'neighborhood', 'city', 'state', 'zipcode'];

        //Dados não preenchidos
        if (!$request->has($fields) || !$request->filled($fields) ) return false;

        //Carrega parametros enviados
        $data = $request->all(); 

        //Se não existir token registrado para a sessão
        if (!Auth::check()) return ['error' => ['unity' => 'Para abrir uma solicitação é necessário uma sessão ativa. Registre uma nova sessão com "Registrar nova sessão"!']];
        
        //Dados não preenchidos, adicionar nova instituição
        $unity_model = new UnityModel([
            'user_id'       => Auth::user()->id, 
            'name'          => (string) $data['name'], 
            'address'       => (string) $data['address'],
            'number'        => (int) $data['number'],
            'complement'    => (string) ($request->has('complement'))? $data['complement'] : '',
            'neighborhood'  => (string) $data['neighborhood'],
            'city'          => (string) $data['city'],
            'state'         => (string) $data['state'],
            'zipcode'       => (int) $data['zipcode']
        ]); 

        //Atribuir resposta ao salvar no banco, erro se não foi possível
        if ($unity_model->save()){
            $response = ['success' => ['unity' => 'Nova unidade adicionada com sucesso.']];
        } else {
            $response = ['error' => ['unity' => 'Houve um erro ao submeter, tente novamente mais tarde.']];
        }

        //Retornar resposta
        return response()->json($response); 
    }

    function delete(Request $request, int $unityID) {

        //Remover unidades
        if (!UnityModel::where("ID", "=", $unityID)->exists()) 
            ['error' => ['unity' => 'Unidade não existe ou já foi removida!']];

        //Retorna a requisição pelo ID
        $unity_model = UnityModel::where([
            ["ID", "=", $unityID],
            ["user_id", "=", Auth::user()->id]
        ])->first();
        
        //Se nulo, continua processo
        return ($unity_model->delete())? ['success' => ['unity' => 'Unidade excluída!']] : ['error' => ['unity' => 'Erro ao excluir unidade!']];

    }

}
