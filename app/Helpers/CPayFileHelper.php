<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 25/10/2016
 * Time: 08:46
 */

namespace App\Helpers;


use Integracao\ControlPay\Helpers\SerializerHelper;

class CPayFileHelper
{
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
            list($key, $value) = $file->fgetcsv('=');

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

}