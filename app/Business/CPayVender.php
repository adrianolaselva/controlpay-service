<?php

namespace App\Business;

use App\Helpers\CPayFileHelper;
use App\Models\Request;
use Integracao\ControlPay;
use JMS\Serializer\Exception\Exception;

/**
 * Class CPayVender
 * @package App\Business
 */
class CPayVender
{
    CONST VENDERAPI_VENDER = 'venderapi/vender';

    /**
     * @var ControlPay\Client
     */
    private $cPayclient;

    /**
     * CPayVender constructor.
     */
    public function __construct(ControlPay\Client $cPayclient)
    {
        $this->cPayclient = $cPayclient;
    }

    /**
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function vender(array $data)
    {
        $responseContent = null;

        try{


            $venderRequest = ControlPay\Helpers\SerializerHelper::denormalize(
                $data,ControlPay\Contracts\Venda\VenderRequest::class);

            $requestModel = Request::create([
                'cnpj' => $data['identificador'],
                'api' => 'venda/vender',
                'method' => 'POST',
                'status_code' => '',
                'body' => json_encode($venderRequest, JSON_PRETTY_PRINT),
                'response_body' => ''
            ]);

            exit();

            $venderApi = new ControlPay\API\VendaApi($this->cPayclient);

            $venderResponse = $venderApi->vender($venderRequest);

            $requestModel->status_code = $venderApi->response->getStatusCode();
            $requestModel->response_body = $venderApi->response->json();
            $requestModel->save();

            $responseContent = CPayFileHelper::convertObjectToFile(
                $venderResponse->getIntencaoVenda(),
                'data.intencaoVenda.'
            );

            if(!empty($venderResponse->getIntencaoVenda()->getProdutos()))
            {
                foreach ($venderResponse->getIntencaoVenda()->getProdutos() as $key => $produto)
                {
                    $responseContent .= CPayFileHelper::convertObjectToFile(
                        $produto,
                        sprintf("data.intencaoVenda.produtos.%s.", $key)
                    );
                }
            }
        }catch (\Exception $ex){
            var_dump($ex->getMessage());
            throw new \Exception("Falha ao enviar requisição", $ex->getCode(), $ex);
        }

        return $responseContent;
    }
}