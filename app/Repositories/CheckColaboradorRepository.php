<?php

namespace App\Repositories;

use App\Interfaces\CheckColaboradorRepositoryInterface;
use App\Models\CheckColaborador;
use Illuminate\Support\Facades\Storage;

class CheckColaboradorRepository extends BaseRepository implements CheckColaboradorRepositoryInterface
{
    protected $model;

    public function __construct(CheckColaborador $model) {
        $this->model = $model;
    }


}
