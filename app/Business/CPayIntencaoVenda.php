<?php

namespace App\Business;

use App\Helpers\CPayFileHelper;
use App\Models\File;
use App\Models\Request;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;
use Illuminate\Support\Facades\Log;
use Integracao\ControlPay;

/**
 * Class CPayIntencaoVenda
 * @package App\Business
 */
class CPayIntencaoVenda
{
    CONST API_INTENCAO_VENDA_GET_BY_ID = '/intencaovenda/getbyid';

    /**
     * @var ControlPay\Client
     */
    private $cPayclient;
    /**
     * @var ControlPay\API\IntencaoVendaApi
     */
    private $intencaoVendaApi;
    /**
     * @var File
     */
    private $fileModel;

    /**
     * CPayIntencaoVenda constructor.
     */
    public function __construct(ControlPay\Client $cPayclient, File $fileModel)
    {
        $this->cPayclient = $cPayclient;
        $this->intencaoVendaApi = new ControlPay\API\IntencaoVendaApi($this->cPayclient);
        $this->fileModel = $fileModel;
    }

    /**
     * @param $data
     * @return null|string
     * @throws \Exception
     */
    public function carregar($data)
    {
        $responseContent = null;
        $requestModel = null;

        try {

//            if(empty($data['intencaoVendaId']));
//                throw new \Exception("Parâmetro 'intencaoVendaId' não encontrado");

            $requestModel = $this->saveRequest();

            $getByIdResponse = $this->intencaoVendaApi->getById($data['intencaoVendaId']);

            $this->saveResponse($requestModel, $this->intencaoVendaApi->getResponse());

            $responseContent = CPayFileHelper::exportIntencaoVenda($getByIdResponse->getIntencaoVenda());

        }catch (RequestException $ex){
            Log::error($ex->getMessage());
            $this->saveResponseException($requestModel, $ex);
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }

        return $responseContent;
    }

    /**
     * @param array $body
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function saveRequest($body = [])
    {
        try{
            return $this->fileModel->requests()->create([
                'req_host' => $this->cPayclient->getParameter(ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_HOST),
                'req_api' => self::API_INTENCAO_VENDA_GET_BY_ID,
                'req_method' => \Illuminate\Http\Request::METHOD_POST,
                'req_headers' => json_encode(
                    $this->intencaoVendaApi->getHeaders(), JSON_PRETTY_PRINT
                ),
                'req_params' => json_encode(
                    $this->intencaoVendaApi->getQueryParameters(), JSON_PRETTY_PRINT
                ),
                'req_body' => json_encode(
                    $body, JSON_PRETTY_PRINT
                ),
                'resp_status' => '',
                'resp_body' => '',
                'created_at' => Carbon::now()
            ]);
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
        }
    }

    /**
     * @param Request $requestModel
     * @param ResponseInterface $response
     */
    private function saveResponse(Request $requestModel, ResponseInterface $response)
    {
        try{
            $requestModel->resp_status = $response->getStatusCode();
            $requestModel->resp_body = json_encode(
                $response->json(), JSON_PRETTY_PRINT
            );
            $requestModel->save();
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
        }
    }

    /**
     * @param Request $requestModel
     * @param RequestException $ex
     */
    private function saveResponseException(Request $requestModel, RequestException $ex)
    {
        try{
            $requestModel->resp_status = $ex->getResponse()->getStatusCode();
            $requestModel->resp_body = json_encode(
                $ex->getResponse()->json(), JSON_PRETTY_PRINT
            );
            $requestModel->save();
        }catch (\Exception $ex){
            Log::error($ex->getMessage());
        }
    }
}