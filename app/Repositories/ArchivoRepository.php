<?php

namespace App\Repositories;

use App\Models\Archivo;
use App\Interfaces\ArchivoRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ArchivoRepository extends BaseRepository implements ArchivoRepositoryInterface
{

    public function __construct()
    {
    }

    public function uploadFile($archivo, $id)
    {
        $filename = $archivo->getClientOriginalName();
        $extension = $archivo->getClientOriginalExtension();
        $fileUuid = uniqid('', true) . '.' . $extension;
        $path = $archivo->storeAs('public/archivos', $fileUuid);
        $url = Storage::url($path);
        return array(
            "idcontrol" => $id,
            "nombre" => $filename,
            "uuid" => $fileUuid,
            "url" => $url,
            "extension" => $extension
        );
    }
    public function uploadFileBase64($archivo, $id)
    {

        $filename = $archivo->name;
        $extension = $archivo->extension;
        $fileUuid = uniqid('', true) . '.' . $extension;

        $documento = base64_decode($archivo->base64);

        $path = Storage::putFileAs('public/archivos', new \Illuminate\Http\File($documento), $fileUuid);

        return array(
            "idcontrol" => $id,
            "nombre" => $filename,
            "uuid" => $fileUuid,
            "url" => $path,
            "extension" => $extension
        );
    }
}
