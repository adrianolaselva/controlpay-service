<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models;

/**
 * Class FileController
 * @package App\Http\Controllers
 */
class FileController extends Controller
{

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function load(Request $request, $id)
    {
        $file = Models\File::find($id);

        if(!$file)
            return response()->json([
                'status' => -1,
                'message' => 'Registro não encontrado'
            ], Response::HTTP_OK);

        $file->content = json_decode($file->content);

        return response()->json($file, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function listAll(Request $request)
    {
        $files = Models\File::select()
            ->skip(empty($request->query('start')) ? 0 : $request->query('start'))
            ->take(empty($request->query('end')) ? 10 : $request->query('end'))
            ->get();

        foreach ($files as $key => $file)
        {
            $files[$key]->content = json_decode($file->content);
        }

        return response()->json($files, Response::HTTP_OK);
    }



    /**
     * @param Request $request
     * @param $path
     * @param $name
     * @return Response
     */
    public function download(Request $request, $path, $name)
    {
        if(!Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", $path, $name)))
            return response('Arquivo não encontrado', Response::HTTP_BAD_REQUEST);

        return response()->download(
            Storage::disk(env('STORAGE_CONFIG'))
                ->getDriver()
                ->getAdapter()
                ->applyPathPrefix(sprintf("%s/%s", $path, $name), $name, [
                    'Content-Type' => 'text/plain'
                ])
        );
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
     * @return mixed
     */
    public function upload(Request $request, $path)
    {

        if(!$request->hasFile('arquivo'))
            return response()->json([
                'status' => -1,
                'message' => 'Arquivo não encontrado!!'
            ], Response::HTTP_BAD_REQUEST);

        $file = $request->file('arquivo');

        if(Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", $path, $file->getClientOriginalName())))
            return response()->json([
                'status' => -1,
                'message' => "Já contém um arquivo com o mesmo nome no path $path/"
            ], Response::HTTP_OK);

        if(Storage::disk(env('STORAGE_CONFIG'))->exists(sprintf("%s/%s", "resp", $file->getClientOriginalName())))
            return response()->json([
                'status' => -1,
                'message' => "Já contém um arquivo com o mesmo nome no path resp/"
            ], Response::HTTP_OK);

        Storage::disk(env('STORAGE_CONFIG'))->put(sprintf("%s/%s", $path, $file->getClientOriginalName()), File::get($file));

        return response()->json([
            'status' => 0,
            'message' => 'Upload realizado com sucesso'
        ], Response::HTTP_OK);
    }

}