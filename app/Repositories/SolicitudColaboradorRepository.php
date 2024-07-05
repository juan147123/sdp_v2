<?php

namespace App\Repositories;

use App\Models\SolicitudColaborador;
use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudColaboradorRepository extends BaseRepository implements
    SolicitudColaboradorRepositoryInterface
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
    public function __construct(SolicitudColaborador $model)
    {
        $this->model = $model;
    }

    public function updateStatusMasive($status, $ids)
    {
        return $this->model->whereIn('id', $ids)->update(['status' => $status]);
    }
    public function getSolicitudColaboradorPendinte($idSolicitud,$status)
    {
        $data = $this->model
            ->where('status', $status)
            ->where('id_solicitud', $idSolicitud)
            ->get()
            ->count();
        return $data;
    }
    
    public function CountSolicitudByUserId($user_id)
    {
        return $this->model->where('user_id', $user_id)->count();
    }
    
}
