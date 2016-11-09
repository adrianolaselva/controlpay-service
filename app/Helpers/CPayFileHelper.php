<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Integracao\ControlPay\Helpers\SerializerHelper;
use Integracao\ControlPay\Model\IntencaoVenda;

/**
 * Class CPayFileHelper
 * @package App\Helpers
 */
class CPayFileHelper
{
    const PATH_CONFIG = '/conf';
    const PATH_REQ = '/req';
    const PATH_RESP = '/resp';
    const PATH_ERROR = '/error';
    const PATH_PROCCESSED = '/proccessed';

    /**
     * Converte conteúdo do arquivo para um array
     *
     * @param $path
     * @param $fileName
     * @return array
     */
    public static function convertFileContentToArray($path, $fileName)
    {
        $data = [];

        $file = new \SplFileObject(sprintf("%s/%s",$path, $fileName));


        while (!$file->eof())
        {
            $row = $file->fgetcsv('=');

            if(empty($row[0]) & empty($row[1]))
                continue;

            list($key, $value) = $row;

            if($key == 'api')
            {
                $data['api'] = $value;
                continue;
            }

            if($key == 'identificador')
            {
                $data['identificador'] = $value;
                continue;
            }

            if($key == 'referencia')
            {
                $data['referencia'] = $value;
                continue;
            }

            $param = explode('.',$key);
            if(isset($param[0]) && $param[0] == 'param')
            {
                unset($param[0]);
                $paramAux = implode('__', $param);
                $data[$paramAux] = $value;
            }
        }

        return ArrayHelper::generateArrayByDelimiter($data);
    }

    /**
     * Retorna string para ser salva em arquivo txt
     *  Formato
     *      chave=valor
     *
     * @param $obejct
     * @param string $baseName
     * @return string
     */
    public static function convertObjectToFile($obejct, $baseName = '')
    {
        $content = '';
        foreach (SerializerHelper::toArray($obejct) as $key => $value)
        {
            if(is_array($value))
                continue;

            $content .= sprintf("%s%s=%s%s", $baseName, $key, $value, PHP_EOL);
        }

        return $content;
    }

    /**
     * @param $file
     * @param $responseContent
     */
    public static function fileCreated($file, $responseContent)
    {
        try{
            $responseStatus = sprintf("response.status=%s%s", 0, PHP_EOL);
            $responseStatus .= sprintf("response.message=%s%s", "Dados processados com sucesso", PHP_EOL);

            Storage::disk(env('STORAGE_CONFIG'))->put(
                sprintf("%s/%s", self::PATH_RESP, basename($file)),
                $responseStatus . $responseContent
            );
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao mover arquivo [%s/%s] => [%s]',
                self::PATH_REQ, basename($file), $ex->getMessage()));
        }
    }

    /**
     * @param $file
     * @param $responseContent
     */
    public static function fileProccessed($file, $responseContent)
    {
        try{
            $responseStatus = sprintf("response.status=%s%s", 0, PHP_EOL);
            $responseStatus .= sprintf("response.message=%s%s", "Dados processados com sucesso", PHP_EOL);

            Storage::disk(env('STORAGE_CONFIG'))->put(
                sprintf("%s/%s", self::PATH_RESP, basename($file)),
                $responseStatus . $responseContent
            );

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                sprintf("%s/%s_%s", self::PATH_PROCCESSED, date('Y-m-d_His'), basename($file))
            );
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao mover arquivo [%s/%s] => [%s]',
                self::PATH_REQ, basename($file), $ex->getMessage()));
        }
    }

    /**
     * @param \Exception $ex
     * @param $file
     */
    public static function fileProccessedError(\Exception $ex, $file)
    {
        try{
            $resposeContent = sprintf("response.status=%s%s", -1, PHP_EOL);
            $resposeContent .= sprintf("response.message=%s%s", $ex->getMessage(), PHP_EOL);

            Storage::disk(env('STORAGE_CONFIG'))->put(
                sprintf("%s/%s", self::PATH_RESP, basename($file)),
                $resposeContent
            );

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                sprintf("%s/%s_%s", self::PATH_ERROR, date('Y-m-d_His'),basename($file))
            );

        }catch (\Exception $ex){
            var_dump($ex->getMessage());
            Log::error(sprintf('Falha ao mover arquivo [%s/%s] => [%s]',
                self::PATH_REQ, basename($file), $ex->getMessage()));
        }
    }

    /**
     * @param $file
     * @return array
     */
    public static function loadFileContent($file)
    {
        $data = [];

        try{
            $data = self::convertFileContentToArray(sprintf("%s%s", self::getBaseDirectory(), self::PATH_REQ), basename($file));
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao processar arquivo %s/%s => [%s]',
                self::PATH_REQ, basename($file)), $ex->getMessage());
        }

        return $data;
    }

    /**
     * @param array $intencoesVenda
     * @return null|string
     */
    public static function exportIntencoesVenda(array $intencoesVenda)
    {
        $responseContent = '';

        foreach ($intencoesVenda as $key => $intencaoVenda)
        {
            $responseContent .= self::exportIntencaoVenda($intencaoVenda, sprintf("data.intencaoVenda.%s", $key));
        }

        return $responseContent;
    }

    /**
     * @param IntencaoVenda $intencaoVenda
     * @return null|string
     */
    public static function exportIntencaoVenda(IntencaoVenda $intencaoVenda, $prefix = 'data.intencaoVenda')
    {
        $responseContent = null;

        try{

            if(empty($intencaoVenda))
                throw new \Exception("Objeto vazio!");

            $responseContent = self::convertObjectToFile(
                $intencaoVenda,
                "{$prefix}."
            );

            if(!empty($intencaoVenda->getIntencaoVendaStatus()))
                $responseContent .= self::convertObjectToFile(
                    $intencaoVenda->getIntencaoVendaStatus(),
                    "{$prefix}.intencaoVendaStatus."
                );

            if(!empty($intencaoVenda->getFormaPagamento()))
            {
                $responseContent .= self::convertObjectToFile(
                    $intencaoVenda->getFormaPagamento(),
                    "{$prefix}.formaPagamento."
                );

                if(!empty($intencaoVenda->getFormaPagamento()->getFluxoPagamento()))
                    $responseContent .= self::convertObjectToFile(
                        $intencaoVenda->getFormaPagamento()->getFluxoPagamento(),
                        "{$prefix}.formaPagamento.fluxoPagamento."
                    );
            }


            if(!empty($intencaoVenda->getTerminal()))
                $responseContent .= self::convertObjectToFile(
                    $intencaoVenda->getTerminal(),
                    "{$prefix}.terminal."
                );

            if(!empty($intencaoVenda->getPedido()))
                $responseContent .= self::convertObjectToFile(
                    $intencaoVenda->getPedido(),
                    "{$prefix}.pedido."
                );

            if (!empty($intencaoVenda->getProdutos())) {
                foreach ($intencaoVenda->getProdutos() as $key => $produto) {
                    $responseContent .= self::convertObjectToFile(
                        $produto,
                        sprintf("%s.produtos.%s.", $prefix, $key)
                    );
                }
            }
            
            return $responseContent;
        }catch (\Exception $ex){
            Log::error(sprintf("Falha ao exportar arquivo fn: [%s]", __FUNCTION__));
        }
    }

    public static function getBaseDirectory()
    {
        return sprintf("%s/app",
            dirname(Storage::disk(env('STORAGE_CONFIG'))->getAdapter()->getPathPrefix())
        );
    }

}