<?php
/**
 * Created by PhpStorm.
 * User: a.moreira
 * Date: 26/10/2016
 * Time: 09:30
 */

namespace App\Http\Controllers;


use App\Models;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequestController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function load(Request $request, $id)
    {
        $request = Models\Request::find($id);

        if(!$request)
            return response()->json([
                'status' => -1,
                'message' => 'Registro não encontrado'
            ], Response::HTTP_OK);

        $request->req_body = json_decode($request->req_body);
        $request->resp_body = json_decode($request->resp_body);

        return response()->json($request, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function listAll(Request $request)
    {
        $requests = Models\Request::all();

        foreach ($requests as $key => $request)
        {
            $requests[$key]->req_body = json_decode($request->req_body);
            $requests[$key]->resp_body = json_decode($request->resp_body);
        }

        return response()->json($requests, Response::HTTP_OK);
    }
}