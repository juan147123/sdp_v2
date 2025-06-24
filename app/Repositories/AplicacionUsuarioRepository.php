<?php

namespace App\Repositories;

use App\Models\AplicacionUsuario;
use App\Interfaces\AplicacionUsuarioRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AplicacionUsuarioRepository extends BaseRepository implements AplicacionUsuarioRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(AplicacionUsuario $model)
    {
        $this->model = $model;
    }
    
    public function listAllActive(){
        return $this->model
        ->select('aplicacion_usuario.*','rol_aplicacion.nombre as nombre_rol','usuario_rol.objeto_permitido')
        ->leftjoin("usuario_rol", "usuario_rol.id_aplicacion_usuario", "=", "aplicacion_usuario.id_aplicacion_usuario")
        ->leftjoin("rol_aplicacion", "rol_aplicacion.id_rol", "=", "usuario_rol.id_rol")
        ->where("aplicacion_usuario.estado_sesion", 1)
        ->where("aplicacion_usuario.id_aplicacion",  $_ENV['ID_APLICACION'])
        ->orderby("aplicacion_usuario.id_aplicacion",  'desc')
        ->get();
    }

    public function findUserByEmail($email)
    {
        return $this->model
            ->where("username", $email)
            ->where("estado_sesion", 1)
            ->where("id_aplicacion",  $_ENV['ID_APLICACION'])
            ->first();
    }
}
