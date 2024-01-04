<?php

namespace App\Repositories;

use App\Models\UsuarioRol;
use App\Interfaces\UsuarioRolRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsuarioRolRepository extends BaseRepository implements
    UsuarioRolRepositoryInterface
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
    public function __construct(UsuarioRol $model)
    {
        $this->model = $model;
    }
    
    public function getIdRol()
    {
        return $this->model
            ->select("id_rol")
            ->where("id_aplicacion_usuario", Auth::user()->id_aplicacion_usuario)
            ->first();
    }
}
