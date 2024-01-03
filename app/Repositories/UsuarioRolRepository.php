<?php

namespace App\Repositories;

use App\Models\UsuarioRol;
use App\Interfaces\UsuarioRolRepositoryInterface;
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
    
}
