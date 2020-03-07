<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Planeta;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class PlanetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Planeta::all();
    }

    /**
     * Mostra as informações de visitas de planetas e quem visitou
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function listaVisita(){

        // busca na base o nome do planeta
        $nome_planeta = Planeta::distinct()->get(['nome_planeta']);

        if($nome_planeta->isNotEmpty()){

           foreach($nome_planeta as $key => $np){

               $arrVisita = Planeta::where('nome_planeta', $np->nome_planeta)->whereNotNull('id_user')->distinct()->get(['id_user']);

               $qtdv['visitantes'] = $arrVisita;

               $data['items'][$key] = [
                 'nome_planeta' => $np->nome_planeta,
                 'id' => $arrVisita,
             ];
           }

           return response()->json($data, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
                                                JSON_UNESCAPED_UNICODE);
        }else{
            return "Não há planetas cadastrados, joven padawan.";
        }

    }

    /**
     * Mostra os usuarios com mais visitas em planetas
     *
     */
    public function topVisitasUser()
    {
       $sql = DB::table('planetas')
                   ->select('id_user', 'id_user', DB::raw('count(id_user) as qtd'))
                   ->whereNotNull('id_user')
                   ->groupBy('id_user')
                   ->orderBy('qtd', 'desc')
                   ->get();

        if($sql > "0"){
            return response()->json($sql, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
                                JSON_UNESCAPED_UNICODE);
        }else{
            return "Nenhuma visita encontrada";
        }


    }

    /**
     * Mostra as informações do planeta cadastrado
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function infoPlaneta(Request $request){

        $planeta = strtolower($request->planeta);

        // busca na base o nome do planeta
        $nome_planeta = Planeta::where('nome_planeta', '=', $planeta)->distinct()->get(['nome_planeta']);

        // testa se o planeta está cadastrado na base
        if($nome_planeta->isNotEmpty()){
            //monta a url para buscar dados na API
            $url = "https://swapi.co/api/planets/?search=".$planeta;

            //monta um array com os dados do planeta
            $arrPlaneta = Helper::getJsonFromUrlDecode($url);
            $qtd['quantidade_visitas'] = Planeta::whereNotNull('id_user')->where('nome_planeta', '=', $planeta)->count();

            $json = array_merge($arrPlaneta, $qtd);

            return response()->json($json, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
                                                                JSON_UNESCAPED_UNICODE);
        }else{
            return  "Planeta não cadastrado no sistema";
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::where('id' , $request->id_user)->get()->first();

        if($user){
            if($request->nome_planeta && $request->id_user){
                $request = array_map('strtolower', $request->all());
                Planeta::create($request);
                return "Planeta cadastrado! Lord Vader!";
            }else{
                return "Informar nome planeta e usuário você deve!";
            }
        }else{
            return "O usuário informado não foi cadastrado no sistema.";
        }

    }
}
