<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Interfaces\UsuarioRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UsuarioRepository extends BaseRepository implements
    UsuarioRepositoryInterface
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
    public function __construct(Usuario $model)
    {
        $this->model = $model;
    }
    public function findByEmail($email)
    {
        return $this->model
            ->select('id_area', 'rol', 'pais')
            ->where('email', $email)
            ->first();
    }
    public function findByIdArea($id)
    {
        return $this->model
            ->select("id", "email", "name")
            ->where("id_area", $id)
            ->where("enable", 1)
            ->get();
    }
}
