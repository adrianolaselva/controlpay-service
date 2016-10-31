<?php

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

        $request->req_headers = json_decode($request->req_headers);
        $request->req_params = json_decode($request->req_params);
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
        $requests = Models\Request::select()
            ->skip(empty($request->query('start')) ? 0 : $request->query('start'))
            ->take(empty($request->query('end')) ? 10 : $request->query('end'))
            ->get();

        foreach ($requests as $key => $request)
        {
            $requests[$key]->req_headers = json_decode($request->req_headers);
            $requests[$key]->req_params = json_decode($request->req_params);
            $requests[$key]->req_body = json_decode($request->req_body);
            $requests[$key]->resp_body = json_decode($request->resp_body);
        }

        return response()->json($requests, Response::HTTP_OK);
    }
}