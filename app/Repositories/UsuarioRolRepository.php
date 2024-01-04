<?php

namespace App\Repositories;

use App\Models\UsuarioRol;
use App\Interfaces\UsuarioRolRepositoryInterface;
use App\Models\RolAplicacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuarioRolRepository extends BaseRepository implements
    UsuarioRolRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $modelRolApp;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(UsuarioRol $model, RolAplicacion $modelRolApp)
    {
        $this->model = $model;
        $this->modelRolApp = $modelRolApp;
    }

    public function getIdRol()
    {
        $usuario_rol = $this->model
            ->select("id_rol")
            ->where("id_aplicacion_usuario", Auth::user()->id_aplicacion_usuario)
            ->first();

        $descripcion_rol = $this->modelRolApp->select('nombre')->where('id_rol', $usuario_rol->id_rol)->first();
        $usuario_rol['descripcion'] = $descripcion_rol->nombre;

        return $usuario_rol;
    }
}
