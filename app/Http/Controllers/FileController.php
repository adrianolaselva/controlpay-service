<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{

    /**
     * @var string
     */
    private $disk = 'local';

    /**
     * @param Request $request
     * @param $path
     * @param $name
     * @return Response
     */
    public function load(Request $request, $path, $name)
    {
        if(!Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", $path, $name)))
            return response('Arquivo não encontrado', Response::HTTP_BAD_REQUEST);

        return Storage::disk(env('STORAGE_CONFIG'))->get(sprintf("%s/%s", $path, $name), $request->getContent());
    }

    /**
     * @param Request $request
     * @param $path
     */
    public function add(Request $request, $path, $name)
    {

        if(Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", $path, $name)))
            return response()->json([
                'status' => -1,
                'message' => "Já contém um arquivo com o mesmo nome no path $path/"
            ], Response::HTTP_OK);

        if(Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", "resp", $name)))
            return response()->json([
                'status' => -1,
                'message' => "Já contém um arquivo com o mesmo nome no path resp/"
            ], Response::HTTP_OK);

        Storage::disk(env('STORAGE_CONFIG'))->put(sprintf("%s/%s", $path, $name), $request->getContent());

        return response()->json([
            'status' => 0,
            'message' => 'Arquivo adicionado com sucesso'
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $path
     */
    public function delete(Request $request, $path, $name)
    {
        if(!Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", $path, $name)))
            return response()->json([
                'status' => 0,
                'message' => 'Arquivo não encontrado'
            ], Response::HTTP_BAD_REQUEST);

        Storage::disk(env('STORAGE_CONFIG'))->delete(sprintf("%s/%s", $path, $name));

        return response()->json([
            'status' => 0,
            'message' => 'Arquivo removido com sucesso'
        ], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $path
     */
    public function upload(Request $request, $path, $name)
    {

        if(!$request->hasFile('arquivo'))
            return response()->json([
                'status' => -1,
                'message' => 'Arquivo não encontrado!!'
            ], Response::HTTP_BAD_REQUEST);

        $file = $request->file('arquivo');

        if(Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", $path, $name)))
            return response()->json([
                'status' => -1,
                'message' => "Já contém um arquivo com o mesmo nome no path $path/"
            ], Response::HTTP_OK);

        if(Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", "resp", $name)))
            return response()->json([
                'status' => -1,
                'message' => "Já contém um arquivo com o mesmo nome no path resp/"
            ], Response::HTTP_OK);

        Storage::disk(env('STORAGE_CONFIG'))->put(sprintf("%s/%s", $path, $name), File::get($file));

        return response()->json([
            'status' => 0,
            'message' => 'Upload realizado com sucesso'
        ], Response::HTTP_OK);
    }

}