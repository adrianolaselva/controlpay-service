<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 26/10/2016
 * Time: 09:17
 */

namespace App\Http\Controllers;

use App\Models;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Integracao\ControlPay\API\IntencaoVendaApi;
use Integracao\ControlPay\Client;

/**
 * Class CallBackController
 * @package App\Http\Controllers
 */
class CallBackController extends Controller
{

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function load(Request $request, $id)
    {
        $callBack = Models\CallBack::find($id);

        if(!$callBack)
            return response()->json([
                'status' => -1,
                'message' => 'Registro não encontrado'
            ], Response::HTTP_OK);

        $callBack->params = json_decode($callBack->params);
        $callBack->body = json_decode($callBack->body);

        return response()->json($callBack,Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function listAll(Request $request)
    {
        $callBacks = Models\CallBack::all();
        foreach ($callBacks as $key => $callBack)
        {
            $callBacks[$key]->params = json_decode($callBack->params);
            $callBacks[$key]->body = json_decode($callBack->body);
        }

        return response()->json($callBacks,Response::HTTP_OK);
    }

    public function controlPayIntencaoVendaCallBack(Request $request)
    {
        try{

            $params = $request->query->all();
            Log::info(sprintf("ControlPayCallBack => %s", var_export($params, true)));

            $file = Models\File::where('reference', $params['intencaoVendaReferencia'])->first();

            if(empty($file))
                return response()->json([
                    'status' => -1,
                    'message' => 'referência não encontrada'
                ],Response::HTTP_BAD_REQUEST);

            $file->callBacks()->create([
                'host' => $request->getClientIp(),
                'api' => $request->getBasePath(),
                'method' => $request->getMethod(),
                'params' => json_encode($params, JSON_PRETTY_PRINT),
                'body' => json_encode($request->getContent(), JSON_PRETTY_PRINT),
                'created_at' => Carbon::now()
            ]);

            if(!Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("conf/%s", $file->identifier)))
                return response()->json([
                    'status' => -1,
                    'message' => 'arquivo de configurações não encontrado'
                ],Response::HTTP_BAD_REQUEST);

            $intencaoVendaApi = new IntencaoVendaApi(new Client(
                parse_ini_string(Storage::disk(env('STORAGE_CONFIG'))->get(sprintf("conf/%s", $file->identifier)))
            ));

            $intencaoVendaApi->getById($params['intencaoVendaId']);
            
            $file->requests()->create([
                'req_host' => $intencaoVendaApi->getResponse()->getEffectiveUrl(),
                'req_api' => '/intencaoVenda/getById',
                'req_method' => 'POST',
                'req_params' => json_encode([
                    'intencaoVendaId' => $params['intencaoVendaId']
                ]),
                'req_body' => '',
                'resp_status' => $intencaoVendaApi->getResponse()->getStatusCode(),
                'resp_body' => json_encode($intencaoVendaApi->getResponse()->json(), JSON_PRETTY_PRINT),
                'created_at' => Carbon::now()
            ]);

            return response()->json([
                'status' => 0,
                'message' => 'CallBack processado com sucesso',
            ],Response::HTTP_OK);
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'status' => -1,
                'message' => $ex->getMessage()
            ],Response::HTTP_BAD_REQUEST);
        }
    }

}