<?php

namespace App\Repositories;

use App\Models\Terminos;
use App\Interfaces\TerminosRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TerminosRepository extends BaseRepository implements TerminosRepositoryInterface
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
    public function __construct(Terminos $model)
    {
        $this->model = $model;
    }
}
