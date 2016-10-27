<?php

namespace App\Business;

use App\Helpers\CPayFileHelper;
use App\Models\Request;
use Carbon\Carbon;
use Integracao\ControlPay;

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
     * @var ControlPay\API\VendaApi
     */
    private $venderApi;

    /**
     * CPayVender constructor.
     */
    public function __construct(ControlPay\Client $cPayclient)
    {
        $this->cPayclient = $cPayclient;
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

        try{

            $venderRequest = ControlPay\Helpers\SerializerHelper::denormalize(
                $data,ControlPay\Contracts\Venda\VenderRequest::class);

            $venderResponse = $this->venderApi->vender($venderRequest);

            Request::create([
                'cnpj' => $data['identificador'],
                'api' => 'venda/vender',
                'method' => 'POST',
                'status_code' => $this->venderApi->getResponse()->getStatusCode(),
                'body' => json_encode($venderRequest, JSON_PRETTY_PRINT),
                'response_body' => json_encode($this->venderApi->getResponse()->json(), JSON_PRETTY_PRINT),
                'created_at' => Carbon::now()
            ]);

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
            printf($ex->getMessage());
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }

        return $responseContent;
    }
}