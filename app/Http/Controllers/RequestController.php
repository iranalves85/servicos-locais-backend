<?php

namespace App\Http\Controllers;

use App\Request as RequestModel;
use App\Unity as UnityModel;
use App\Resources as ResourcesModel;
use App\Business as BusinessModel;
use App\Support as SupportModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    function get(Request $request, $paged = 0, $estado = null) {

        //Query inicial
        $query = DB::table('requests AS req')
                    ->join('unity as u', 'req.unity_id', '=', 'u.ID')
                    ->join('request_type as t', 'req.type', '=', 't.ID')
                    ->select("req.ID", "req.updated_at", "req.unity_id", "t.type", "req.goal", "u.user_id", "u.address", "u.zipcode", "u.city", "u.name", "u.neighborhood", "u.number", "u.state", DB::raw("(select group_concat(R.ID, '-', R.name separator ',' ) from resources R where R.request_id = req.ID group by R.request_id order by count(R.request_id)) as items"))
                    ->orderByDesc('req.updated_at')
                    ->offset(($paged > 1)? ($paged - 1) * 10 : 0)
                    ->take(10);

        //Se houve envio de solicitação de filtrar solicitações
        if (!is_null($estado))
        {
            $query->where("u.state", "=", $estado);
        }
        
        //Realiza a query
        $solicitacoes = $query->get();

        //Retorna items estruturados para exibição
        return response()->json($this->mount($solicitacoes));
    }

    function getOwn(Request $request, $paged = 0) {
        
        //Query inicial
        $query = DB::table('requests AS req')
                    ->join('unity as u', 'req.unity_id', '=', 'u.ID')
                    ->join('request_type as t', 'req.type', '=', 't.ID')
                    ->select("req.ID", "req.updated_at", "req.unity_id", "t.type", "req.goal", "u.user_id", "u.address", "u.zipcode", "u.city", "u.name", "u.neighborhood", "u.number", "u.state", DB::raw("(select group_concat(R.ID, '-', R.name separator ',' ) from resources R where R.request_id = req.ID group by R.request_id order by count(R.request_id)) as items"))
                    ->where('u.user_id', '=', Auth::user()->id)
                    ->orderByDesc('req.updated_at')
                    ->offset(($paged > 1)? ($paged - 1) * 10 : 0)
                    ->take(10);
        
        //Realiza a query
        $solicitacoes = $query->get();

        //Retorna items estruturados para exibição
        return response()->json($this->mount($solicitacoes));
    }

    function add(Request $request) {

        //Campos obrigatórios
        $fields = ['unity_id', 'goal', 'type'];
        
        //Dados não preenchidos
        if (!$request->has($fields) || !$request->filled($fields) ) return false;

        //Carrega parametros enviados
        $data = $request->all(); 

        //Se não existir token registrado para a sessão
        if (!Auth::check()) return ['error' => ['request' => 'Para abrir uma solicitação é necessário uma sessão ativa. Registre uma nova sessão com "Registrar nova sessão"!']];

        //Insere modelo no banco registrando timestamps
        $request_model = new RequestModel([
            'unity_id'  => (int) $data['unity_id'], 
            'goal'      => (string) $data['goal'],
            'type'      => (int)    $data['type'],
        ]);

        //Se inserção foi feita corretamente
        if ($request_model->save()) {

            $request_insert_id = $request_model->getAttribute('ID');

            //Adicionando items
            foreach ($data['items'] as $key => $value) {
                //Inserindo dados no banco
                $resource_model = ResourcesModel::create([
                    'name'          => filter_var($value, FILTER_SANITIZE_STRING), 
                    'request_id'    => (int) $request_insert_id
                ]);
            }

            //Retorna mensagem de sucesso
            return response()->json(['success' => ['request' => 'Solicitação aberta com sucesso.']]);

        } else {
            //Retorna mensagem de erro
            return response()->json(['error' => ['request' => 'Não foi possível registrar sua solicitação.']]);
        }

    }

    function delete(Request $request, int $requestID) {

        //Retorna a requisição pelo ID
        $request_model = RequestModel::where("ID", "=", $requestID)->first();
        
        //Se nulo, continua processo
        return ($request_model->delete())? ['success' => ['request' => 'Solicitação excluída!']] : ['error' => ['request' => 'Erro ao excluir solicitação!']];

    }

    private function searchForValue(int $valueToFind, $array) {
        
        $exist = false;
        
        foreach ($array as $key => $value) {
            //Se encontrado atribuir como true
            if (key_exists('ID', $value) && $valueToFind == (int) $value['ID']) {
                //Retorna todos os items de apoios
                $exist = SupportModel::where("resource_id", "=", $valueToFind)->get();
                break;
            }
        }

        return $exist;
    }

    private function mount(\Illuminate\Support\Collection $solicitacoes) {
        
        //Se existir dados a interar
        if ($solicitacoes->count() > 0) {

            //Query que verifica requisições
            $resources = ResourcesModel::
            join("requests as req", "resources.request_id", "=", "req.ID")
            ->select("resources.ID")
            ->get()->toArray();    
            
            //Atribui Id de usuário de contexto
            $user_id = Auth::user()->id;

            foreach ($solicitacoes->all() as $key => $value) {
                
                if (property_exists($value, 'items') && !empty($value->items)) {
                    
                    //Transforma itens em array
                    $items = explode(',', $value->items);

                    //Percorre array de items
                    foreach ($items as $k => $v) {

                        //$res[0] = ID
                        //$res[1] = NOME
                        //$res[2] = BUSINESS ID
                        //$res[3] = EMPRESA
                        $res = explode('-', $v);

                        //Se existe token registrado na sessão
                        if ($user_id == $value->user_id && isset($resources) && $supportFound = $this->searchForValue((int) $res[0], $resources)) {

                            foreach ($supportFound as $index => $current) {
                                
                                //Retorna dados da empresa atribuido ao recurso
                                $businessArray = BusinessModel::where("ID", "=", $current->business_id)->get()->toArray();

                                //Atribui empresa ao array principal
                                if (count($businessArray) > 0) {
                                    //Adicionando a variavel
                                    $empresa = $businessArray[key($businessArray)];

                                    //Carregando unidade 
                                    $unidade = UnityModel::where('ID', '=', $empresa['unity_id'])->first();
                                    
                                    //Atribuindo resposta
                                    $res[2][] = [
                                        'unity'  => $unidade['name'],
                                        'telefone'  => $empresa['telephone'],
                                        'email'     => $empresa['email']
                                    ];
                                }
                            }
                            
                        }

                        $items[$k] = $res; //Atribuindo ao array
                        
                    }

                    //Adicionando ao array geral
                    $solicitacoes[$key]->items = [];
                    $solicitacoes[$key]->items[] = $items; 

                }

                //Adicionando atributo que permite edição da solicitação
                if (isset($request_token) && $user_id == $value->ID) {
                    $solicitacoes[$key]->can_edit = true;
                }
            }
        }

        return $solicitacoes;
    }

}
