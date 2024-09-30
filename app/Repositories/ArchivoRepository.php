<?php

namespace App\Repositories;

use App\Interfaces\ArchivoRepositoryInterface;
use App\Models\Archivos;
use Illuminate\Support\Facades\Storage;

class ArchivoRepository extends BaseRepository implements ArchivoRepositoryInterface
{
    protected $model;

    public function __construct(Archivos $model) {
        $this->model = $model;
    }

    public function uploadFile($archivos, $id, $new_solicitud, $origen,$titulo)
    {
        $resultados = [];
        foreach ($archivos as $archivo) {
            $filename = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $uuid = uniqid('', true);
            $fileUuid = $uuid . '.' . $extension;
            $carpeta = date('Ymd') . $new_solicitud->codigo;
            $path = $archivo->storeAs('public/' . $carpeta, $fileUuid);
            $url = Storage::url($path);

            $resultados[] = array(
                "id_solicitud_colaborador" => $id,
                "name" => $filename,
                "path" => $url,
                "extension" => $extension,
                "uuid_name" => $fileUuid,
                "uuid" => $uuid,
                "carpeta" => $carpeta,
                "origen" => $origen,
                "titulo" => $titulo
            );
        }
        return $resultados;
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
