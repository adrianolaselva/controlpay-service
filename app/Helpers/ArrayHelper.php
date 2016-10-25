<?php

namespace App\Helpers;

/**
 * Class ArrayHelper
 * @package App\Helpers
 */
class ArrayHelper
{

    public static function generateArrayByDelimiter($res_assoc, $delimiter = "__")
    {
        $res_return = [];
        if($res_assoc)
        {
            if(isset($res_assoc[0]))
            {
                foreach($res_assoc as $value)
                {
                    $res_return[] = self::generateArrayByDelimiter($value);
                }

                return $res_return;
            }

            foreach ($res_assoc as $key => $value)
            {
                if (strpos($key, $delimiter) === false) {
                    $res_return[$key] = $value;
                } else {
                    $explodeValues = explode($delimiter, $key);
                    $arrayValues = $value;
                    for ($i = count($explodeValues) - 1; $i >= 0; $i--)
                    {
                        if(intval($explodeValues[$i]) || $explodeValues[$i] == '0')
                        {
                            $arrayValues = [
                                str_pad($explodeValues[$i], 3, '0', STR_PAD_LEFT) => $arrayValues
                            ];
                            continue;
                        }

                        $arrayValues = array($explodeValues[$i] => $arrayValues);
                    }

                    $res_return = array_merge_recursive($res_return, $arrayValues);
                }
            }
        }

        return $res_return;
    }

}