<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class ControlPayController
 * @package App\Http\Controllers
 */
class ControlPayController extends Controller
{

    /**
     * ControlPayCallBack constructor.
     */
    public function __construct()
    {

    }

    public function intencaoVendaCallBack(Request $request)
    {
        //intencaoVendaId=&intencaoVendaReferencia=&pedidoId=&pedidoReferencia=
        $params = $request->query->all();
        Log::info(sprintf("ControlPayCallBack => %s", var_export($params, true)));
    }

}