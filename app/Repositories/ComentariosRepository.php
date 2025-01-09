<?php

namespace App\Repositories;

use App\Interfaces\ComentariosRepositoryInterface;
use App\Models\Comentarios;
use Illuminate\Support\Facades\Storage;

class ComentariosRepository extends BaseRepository implements ComentariosRepositoryInterface
{
    protected $model;

    public function __construct(Comentarios $model)
    {
        $this->model = $model;
    }


}
