<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 21/10/2016
 * Time: 12:52
 */

namespace App\Http\Controllers;



use Illuminate\Http\Request;

class ControlPay extends Controller
{

    /**
     * ControlPayCallBack constructor.
     */
    public function __construct()
    {
    }

    public function intencaoVendaCallBack(Request $request)
    {
        $request->query->all();
    }

}