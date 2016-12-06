<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 26/10/2016
 * Time: 09:17
 */

namespace App\Http\Controllers;

use App\Helpers\CPayFileHelper;
use App\Models;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Integracao\ControlPay\API\IntencaoVendaApi;
use Integracao\ControlPay\Client;
use Integracao\ControlPay\Contracts\IntencaoVenda\GetByFiltrosRequest;

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

        $callBack->headers = json_decode($callBack->headers);
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
        $callBacks = Models\CallBack::select()
            ->skip(empty($request->query('start')) ? 0 : $request->query('start'))
            ->take(empty($request->query('end')) ? 10 : $request->query('end'))
            ->get();

        foreach ($callBacks as $key => $callBack)
        {
            $callBacks[$key]->headers = json_decode($callBack->headers);
            $callBacks[$key]->params = json_decode($callBack->params);
            $callBacks[$key]->body = json_decode($callBack->body);
        }

        return response()->json($callBacks,Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function intencaoVendaCallBack(Request $request)
    {
        $responseContent = null;
        try{

            $params = $request->query->all();
            Log::info(sprintf("ControlPayCallBack => %s", var_export($params, true)));

            if(!isset($params['intencaoVendaReferencia']))
                throw new \Exception('Parâmetro [intencaoVendaReferencia] não encontrada');

            $file = Models\File::where('reference', $params['intencaoVendaReferencia'])->first();

            if(empty($file))
                throw new \Exception('Referência não encontrada');

            $file->callBacks()->create([
                'host' => $request->getClientIp(),
                'api' => $request->getRequestUri(),
                'method' => $request->getMethod(),
                'headers' => json_encode(
                    $request->headers->all(), JSON_PRETTY_PRINT
                ),
                'params' => json_encode(
                    $params, JSON_PRETTY_PRINT
                ),
                'body' => json_encode(
                    $request->getContent(), JSON_PRETTY_PRINT
                ),
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

            $response = $intencaoVendaApi->getByFiltros( (new GetByFiltrosRequest())
                ->setIntencaoVendaId($params['intencaoVendaId'])
            );

            if(count($response->getIntencoesVendas()) == 0)
                throw new \Exception('Intenção venda não encontrada a partir do intencaoVendaId');

            $intencaoVenda = $response->getIntencoesVendas()[0];

            $file->requests()->create([
                'req_host' => $intencaoVendaApi->getResponse()->getEffectiveUrl(),
                'req_api' => '/intencaoVenda/getById',
                'req_method' => 'POST',
                'req_headers' => json_encode(
                    $intencaoVendaApi->getHeaders(), JSON_PRETTY_PRINT
                ),
                'req_params' => json_encode(
                    $intencaoVendaApi->getQueryParameters(), JSON_PRETTY_PRINT
                ),
                'req_body' => '',
                'resp_status' => $intencaoVendaApi->getResponse()->getStatusCode(),
                'resp_body' => json_encode($intencaoVendaApi->getResponse()->json(), JSON_PRETTY_PRINT),
                'created_at' => Carbon::now()
            ]);

            CPayFileHelper::fileCreated("callback_$file->name",
                CPayFileHelper::exportGeneric($intencaoVenda, 'intencaoVenda')
            );

            return response()->json([
                'status' => 0,
                'message' => 'CallBack processado com sucesso',
            ],Response::HTTP_OK);
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'status' => -1,
                'message' => $ex->getMessage(),
            ],Response::HTTP_BAD_REQUEST);
        }
    }

}