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
    
    public function findUserByEmail($email)
    {
        return $this->model
            ->where("username", $email)
            ->where("id_aplicacion",  $_ENV['ID_APLICACION'])
            ->first();
    }
}
