<?php

namespace App\Repositories;

use App\Models\Solicitud;
use App\Interfaces\SolicitudRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SolicitudRepository extends BaseRepository implements SolicitudRepositoryInterface
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
    public function __construct(Solicitud $model)
    {
        $this->model = $model;
    }
   
}
