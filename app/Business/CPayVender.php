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
 * Class CPayVender
 * @package App\Business
 */
class CPayVender
{
    CONST API_VENDA_VENDER = '/venda/vender';

    CONST API_VENDA_CANCELAR = '/venda/cancelarvenda';

    /**
     * @var ControlPay\Client
     */
    private $cPayclient;

    /**
     * @var ControlPay\API\VendaApi
     */
    private $venderApi;
    /**
     * @var ControlPay\API\PedidoApi
     */
    private $pedidoApi;
    /**
     * @var File
     */
    private $fileModel;

    /**
     * CPayVender constructor.
     */
    public function __construct(ControlPay\Client $cPayclient, File $fileModel)
    {
        $this->cPayclient = $cPayclient;
        $this->fileModel = $fileModel;
        $this->pedidoApi = new ControlPay\API\PedidoApi($this->cPayclient);
        $this->venderApi = new ControlPay\API\VendaApi($this->cPayclient);
    }

    /**
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function vender(array $data)
    {
        $responseContent = null;
        $requestModel = null;

        try {

            $venderRequest = ControlPay\Helpers\SerializerHelper::denormalize(
                $data, ControlPay\Contracts\Venda\VenderRequest::class);

            $requestModel = $this->saveRequest($venderRequest);

            $venderRequest->setReferencia($data['referencia']);

            $venderResponse = $this->venderApi->vender($venderRequest);

            $this->saveResponse($requestModel, $this->venderApi->getResponse());

            $responseContent = CPayFileHelper::exportIntencaoVenda($venderResponse->getIntencaoVenda());
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
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function cancelarVenda(array $data)
    {
        $responseContent = null;
        $requestModel = null;

        try {

            $cancelarVendaRequest = ControlPay\Helpers\SerializerHelper::denormalize(
                $data, ControlPay\Contracts\Venda\CancelarVendaRequest::class);

            $requestModel = $this->saveRequest($cancelarVendaRequest);

            $cancelarVendaRequest->setReferencia($data['referencia']);

            $cancelarVendaResponse = $this->venderApi->cancelarVenda($cancelarVendaRequest);

            $this->saveResponse($requestModel, $this->venderApi->getResponse());

            $responseContent = CPayFileHelper::exportIntencaoVenda($cancelarVendaResponse->getIntencaoVenda());
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
     * @param ControlPay\Contracts\Venda\VenderRequest $venderRequest
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function saveRequest($venderRequest)
    {
        try{
            return $this->fileModel->requests()->create([
                'req_host' => $this->cPayclient->getParameter(ControlPay\Constants\ControlPayParameterConst::CONTROLPAY_HOST),
                'req_api' => self::API_VENDA_VENDER,
                'req_method' => \Illuminate\Http\Request::METHOD_POST,
                'req_headers' => json_encode(
                    $this->venderApi->getHeaders(), JSON_PRETTY_PRINT
                ),
                'req_params' => json_encode(
                    $this->venderApi->getQueryParameters(), JSON_PRETTY_PRINT
                ),
                'req_body' => json_encode(
                    $venderRequest, JSON_PRETTY_PRINT
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