<?php

namespace App\Http\Controllers;

use App\User as UserModel;
use Egulias\EmailValidator\EmailLexer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Concerns\FilterEmailValidation;

class UserController extends Controller
{
    function isAuth(Request $request) {
        
        //Verifica se usuário esta autenticado
        if (Auth::check()) {
            $user = Auth::user();
            $response = ['success' => ['login' => 'Autorizado', 'user' => $user]]; 
        } else {
            $response = ['error' => ['login' => 'Acesso não permitido.']];
        } 

        return response()->json($response);
    }

    function login(Request $request) {
        
        //campos de registro
        $password = ['password'];
        $pessoal = ['email'];

        //Verificar se campos foram enviados
        if (!$request->has($pessoal) || !$request->filled($pessoal)) 
            return response()->json(['error' => ['register' => 'Campo obrigatórios ausentes.']]);
        
        $filter = new FilterEmailValidation();
        if (!$filter->isValid($request->input('email'), new EmailLexer))
            return response()->json(['error' => ['register' => 'Email não é válido.']]);

        if (!$request->has($password) || !$request->filled($password)) 
            return response()->json(['error' => ['register' => 'Campo de password obrigatório ausentes.']]);

        //Registrando Autenticação
        if (Auth::guard('web')->attempt($request->only('email', 'password'), true)) {
            
            //Retorna modelo de usuário de login
            $user = UserModel::where('email', '=', $request->input('email'))->first();
            
            //Verificando se senhas conferem 
            $response = ['success' => ['login' => 'Login efetuado com sucesso.', 'token' => $user->createToken('login')->plainTextToken, 'username' => $user->name]];

            return response()->json($response);
        }

        //Verificando se senhas conferem 
        $response = ['error' => ['login' => 'Falha na autenticação.']];
        return response()->json($response);
    }

    function register(Request $request) {

        //campos de registro
        $password = ['password', 'confirm_password'];
        $pessoal = ['name', 'email'];

        //Verificar se passwords foram enviados
        if (!$request->has($password) || !$request->filled($password)) 
            return response()->json(['error' => ['register' => 'Campo de passwords obrigatórios ausentes.']]);

        //Verificar se dados pessoais enviados
        if (!$request->has($pessoal) || !$request->filled($pessoal)) 
            return response()->json(['error' => ['register' => 'Campo obrigatórios ausentes.']]);

        //Verificar se senhas enviadas conferem
        if ($request->input('password') != $request->input('confirm_password'))
            return response()->json(['error' => ['register' => 'As senhas não conrespondem.']]);
        
        //Validação de email
        $filter = new FilterEmailValidation();
        if (!$filter->isValid($request->input('email'), new EmailLexer))
            return response()->json(['error' => ['register' => 'Email não é válido.']]);

        //Verificar se usuário/email já existe
        if (UserModel::where('email', '=', (string) $request->input('email'))->exists())
            return response()->json(['error' => ['register' => 'Este email já esta sendo utilizado.']]);

        //Dados a serem inseridos
        $data = [
            'name'  => (string) $request->input('name'),
            'email' => (string) $request->input('email'),
            'password'  => (string) Hash::make($request->input('password'))
        ];

        //Carregando modelo
        $user = new UserModel($data);

        //Salvando dados de novo usuário no BD
        if ($user->save()) {
            //Executar login de usuário
            $result = $this->login($request);
        } else {
            //Retornar respota com erro
            $result = response()->json(['error' => ['register' => 'Houve erro em seu cadastro, revise os dados informados.']]);
        }

        return $result;
    }

    function get(Request $request, $user_id = 0) {

        //Database Modelo 
        $user = new UserModel();    

        //Query inicial
        $result = $user::where('ID', '=', $user_id)->first();

        return response()->json($result);
    }

    function delete(Request $request, $requestID) {


    }

}
