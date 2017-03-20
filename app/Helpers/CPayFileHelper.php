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

        $file = new \SplFileObject(sprintf("%s/%s", $path, $fileName));

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
     * @param $object
     * @param string $baseName
     * @return string
     */
    public static function convertObjectToFile($object, $baseName = '')
    {
        $content = '';

        $array = $object;

        if(is_object($object))
            $array = SerializerHelper::toArray($object);

        if(is_array($array))
            foreach ($array as $key => $value)
            {

                if(is_array($value)){
                    foreach ($value as $keySub => $row){
                        if(!is_string($row) && !is_integer($row))
                            continue;

                        $content .= sprintf("%s%s.%s=%s%s", $baseName, $key, $keySub, $row, PHP_EOL);
                    }

                    continue;
                }

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
            if(!self::fileIsWork($file))
                $file = sprintf("%s.wrk", $file);

            $file = sprintf("%s/%s", self::PATH_RESP, basename($file));

            $responseStatus = sprintf("response.status=%s%s", 0, PHP_EOL);
            $responseStatus .= sprintf("response.message=%s%s", "Dados processados com sucesso", PHP_EOL);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($file))
                Storage::disk(env('STORAGE_CONFIG'))->delete($file);

            Storage::disk(env('STORAGE_CONFIG'))->put(
                $file,
                $responseStatus . $responseContent
            );

            self::fileUnWork($file);
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao mover arquivo [%s/%s] => [%s]',
                self::PATH_REQ, basename($file), $ex->getMessage()));
        }
    }

    /**
     * @param $file
     * @return string
     */
    public static function fileToWork($file)
    {
        try{
            if(self::fileIsWork($file))
                return $file;

            $fileWork = sprintf("%s.wrk", $file);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileWork))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileWork);

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                $fileWork
            );
            return $fileWork;
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao mover arquivo [%s/%s] => [%s]',
                self::PATH_REQ, basename($file), $ex->getMessage()));
        }
    }

    /**
     * @param $file
     * @return string
     */
    public static function fileToLock($file)
    {
        try{
            if(self::fileIsLock($file))
                return $file;

            $fileLock = sprintf("%s.lck", $file);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileLock))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileLock);

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                $fileLock
            );

            return $fileLock;
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao mover arquivo [%s/%s] => [%s]',
                self::PATH_REQ, basename($file), $ex->getMessage()));
        }
    }

    /**
     * @param $file
     * @return string
     */
    public static function fileIsWork($file)
    {
        return (strpos($file, '.wrk') !== false) ? true : false;
    }

    /**
     * @param $file
     * @return string
     */
    public static function fileIsLock($file)
    {
        return (strpos($file, '.lck') !== false) ? true : false;
    }

    /**
     * @param $file
     * @return mixed
     */
    public static function fileUnWork($file)
    {
        try{
            if(!self::fileIsWork($file))
                return $file;

            $fileUnlock = str_replace('.wrk', '', $file);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileUnlock))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileUnlock);

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                $fileUnlock
            );
        }catch (\Exception $ex){
            Log::error(sprintf('Falha ao mover arquivo [%s/%s] => [%s]',
                self::PATH_REQ, basename($file), $ex->getMessage()));
        }
    }

    /**
     * @param $file
     * @return mixed
     */
    public static function fileUnLock($file)
    {
        try{
            if(!self::fileIsLock($file))
                return $file;

            $fileUnlock = str_replace('.lck', '', $file);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileUnlock))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileUnlock);

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                $fileUnlock
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

            if(!self::fileIsWork($file))
                $file = sprintf("%s.wrk", $file);

            $fileResp = sprintf("%s/%s", self::PATH_RESP, basename($file));
            $fileProcessed = sprintf("%s/%s_%s", self::PATH_PROCCESSED, date('Y-m-d_His'), basename($file));

            $responseStatus = sprintf("response.status=%s%s", 0, PHP_EOL);
            $responseStatus .= sprintf("response.message=%s%s", "Dados processados com sucesso", PHP_EOL);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileResp))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileResp);

            Storage::disk(env('STORAGE_CONFIG'))->put(
                $fileResp,
                $responseStatus . $responseContent
            );
            self::fileUnWork($fileResp);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileProcessed))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileProcessed);

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                $fileProcessed
            );
            self::fileUnWork($fileProcessed);

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
            if(!self::fileIsWork($file))
                $file = sprintf("%s.wrk", $file);

            $fileResp = sprintf("%s/%s", self::PATH_RESP, basename($file));
            $fileError = sprintf("%s/%s_%s", self::PATH_ERROR, date('Y-m-d_His'),basename($file));

            $resposeContent = sprintf("response.status=%s%s", -1, PHP_EOL);
            $resposeContent .= sprintf("response.message=%s%s", $ex->getMessage(), PHP_EOL);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileResp))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileResp);

            Storage::disk(env('STORAGE_CONFIG'))->put(
                $fileResp,
                $resposeContent
            );
            self::fileUnWork($fileResp);

            if(Storage::disk(env('STORAGE_CONFIG'))->exists($fileError))
                Storage::disk(env('STORAGE_CONFIG'))->delete($fileError);

            Storage::disk(env('STORAGE_CONFIG'))->move(
                $file,
                $fileError
            );
            self::fileUnWork($fileError);

        }catch (\Exception $ex){
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
            $data = self::convertFileContentToArray(sprintf("%s%s",
                self::getBaseDirectory(), self::PATH_REQ),
                basename($file)
            );

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
            $responseContent .= self::exportGeneric($intencaoVenda,
                sprintf("data.intencaoVenda.%s", $key)
            );
        }

        return $responseContent;
    }

    public static function exportGeneric($objeto, $className){
        $responseContent = '';

        try{
            if(empty($objeto))
                throw new \Exception("Objeto vazio!");

            $prefix = sprintf("data.%s", $className);

            $responseContent = self::convertObjectToFile(
                $objeto,
                sprintf("%s.", $prefix)
            );

            $array = SerializerHelper::toArray($objeto);

            if (isset($array['formaPagamento'])) {
                $responseContent .= self::convertObjectToFile(
                    $array['formaPagamento'],
                    sprintf("%s.formaPagamento.", $prefix)
                );
            }

            if (isset($array['produtos'])) {
                foreach ($array['produtos'] as $key => $produto) {
                    $responseContent .= self::convertObjectToFile(
                        $produto,
                        sprintf("%s.produtos.%s.", $prefix, $key)
                    );
                }
            }

            if (isset($array['pagamentosExternos'])) {
                foreach ($array['pagamentosExternos'] as $key => $pagamentosExterno) {
                    $responseContent .= self::convertObjectToFile(
                        $pagamentosExterno,
                        sprintf("%s.pagamentosExternos.%s.", $prefix, $key)
                    );
                }
            }

            return $responseContent;
        }catch (\Exception $ex){
            var_dump($ex->getMessage());
            Log::error(sprintf("Falha ao exportar arquivo fn: [%s]", __FUNCTION__));
        }
    }

//    /**
//     * @param IntencaoVenda $intencaoVenda
//     * @return null|string
//     */
//    public static function exportIntencaoVenda(IntencaoVenda $intencaoVenda, $prefix = 'data.intencaoVenda')
//    {
//        $responseContent = null;
//
//        try{
//
//            if(empty($intencaoVenda))
//                throw new \Exception("Objeto vazio!");
//
//            $responseContent = self::convertObjectToFile(
//                $intencaoVenda,
//                "{$prefix}."
//            );
//
//            if(!empty($intencaoVenda->getIntencaoVendaStatus()))
//                $responseContent .= self::convertObjectToFile(
//                    $intencaoVenda->getIntencaoVendaStatus(),
//                    "{$prefix}.intencaoVendaStatus."
//                );
//
//            if(!empty($intencaoVenda->getFormaPagamento()))
//            {
//                $responseContent .= self::convertObjectToFile(
//                    $intencaoVenda->getFormaPagamento(),
//                    "{$prefix}.formaPagamento."
//                );
//
//                if(!empty($intencaoVenda->getFormaPagamento()->getFluxoPagamento()))
//                    $responseContent .= self::convertObjectToFile(
//                        $intencaoVenda->getFormaPagamento()->getFluxoPagamento(),
//                        "{$prefix}.formaPagamento.fluxoPagamento."
//                    );
//            }
//
//
//            if(!empty($intencaoVenda->getTerminal()))
//                $responseContent .= self::convertObjectToFile(
//                    $intencaoVenda->getTerminal(),
//                    "{$prefix}.terminal."
//                );
//
//            if(!empty($intencaoVenda->getPedido()))
//                $responseContent .= self::convertObjectToFile(
//                    $intencaoVenda->getPedido(),
//                    "{$prefix}.pedido."
//                );
//
//            if (!empty($intencaoVenda->getProdutos())) {
//                foreach ($intencaoVenda->getProdutos() as $key => $produto) {
//                    $responseContent .= self::convertObjectToFile(
//                        $produto,
//                        sprintf("%s.produtos.%s.", $prefix, $key)
//                    );
//                }
//            }
//
//            if (!empty($intencaoVenda->getPagamentosExternos())) {
//                foreach ($intencaoVenda->getPagamentosExternos() as $key => $pagamentosExterno) {
//                    $responseContent .= self::convertObjectToFile(
//                        $pagamentosExterno,
//                        sprintf("%s.PagamentosExternos.%s.", $prefix, $key)
//                    );
//                }
//            }
//
////            if (!empty($intencaoVenda->getPagamentosExternos())) {
////                foreach ($intencaoVenda->getPagamentosExternos() as $pagamentoExterno) {
////                    foreach ($pagamentoExterno->getComprovanteAdquirenteLinhas() as $key => $linha){
////                        $responseContent .= sprintf(
////                            "%s.PagamentosExternos.ComprovanteAdquirente.%s=%s%s",
////                            $prefix,
////                            $key,
////                            $linha,
////                            PHP_EOL
////                        );
////                    }
////                }
////            }
//
//            return $responseContent;
//        }catch (\Exception $ex){
//            Log::error(sprintf("Falha ao exportar arquivo fn: [%s]", __FUNCTION__));
//        }
//    }

    public static function getBaseDirectory()
    {
        return sprintf("%s/app",
            dirname(Storage::disk(env('STORAGE_CONFIG'))->getAdapter()->getPathPrefix())
        );
    }

}