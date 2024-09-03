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

    public function updateStatusMasiveAdmObr($request)
    {
        return $this->model->whereIn('id', $request->ids)->update([
            'aprobado_administrador_obra' => $request->status,
            "comentario_admin_obra" => $request->comentario_admin_obra
        ]);
    }

    public function updateStatusMasiveVisiObr($request)
    {
        return $this->model->whereIn('id', $request->ids)->update([
            'aprobado_visitador_obra' => $request->status,
            "comentario_visitador" => $request->comentario_visitador
        ]);
    }

    public function updateStatusMasiveRrhh($status, $ids)
    {
        return $this->model->whereIn('id', $ids)->update(['aprobado_rrhh' => $status]);
    }

    public function getSolicitudColaboradorPendinteAdmObr($idSolicitud, $status)
    {
        $data = $this->model
            ->where('aprobado_administrador_obra', $status)
            ->where('id_solicitud', $idSolicitud)
            ->get()
            ->count();
        return $data;
    }

    public function getSolicitudColaboradorPendinteVisiObr($idSolicitud, $status)
    {
        $data = $this->model
            ->where('aprobado_visitador_obra', $status)
            ->where('aprobado_administrador_obra', '!=', 7)
            ->where('id_solicitud', $idSolicitud)
            ->get()
            ->count();
            return $data;
        }
        public function getSolicitudColaboradorPendinteRrhh($idSolicitud, $status)
        {
            $data = $this->model
            ->where('aprobado_rrhh', $status)
            ->where('aprobado_visitador_obra', '!=', 7)
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
