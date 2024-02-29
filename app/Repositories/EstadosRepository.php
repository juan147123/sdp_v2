<?php

namespace App\Repositories;

use App\Models\Estados;
use App\Interfaces\EstadosRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class EstadosRepository extends BaseRepository implements EstadosRepositoryInterface
{
    protected $model;

    public function __construct(Estados $model)
    {
        $this->model = $model;
    }


}
