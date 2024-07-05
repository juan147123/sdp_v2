<?php

namespace App\Http\Controllers;

use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use App\Interfaces\SolicitudRepositoryInterface;
use Illuminate\Http\Request;

class SolicitudColaboradorController extends Controller
{
    private $repository;
    private $repositorySolicitud;

    public function __construct(
        SolicitudColaboradorRepositoryInterface $repository,
        SolicitudRepositoryInterface $repositorySolicitud
    ) {
        $this->repository = $repository;
        $this->repositorySolicitud = $repositorySolicitud;
    }


    public function delete($id)
    {
        $this->repository->update($id, ["enable" => 0]);
    }

    public function updateStatus(Request $request)
    {
        $this->repository->update(
            $request->id,
            $request->except(['id_solicitud'])
        );

        $this->updateStatusSolicitud($request,7);

        return redirect()->route('redirect.solicitud');
    }
    public function updateStatusSolicitud($request, $status)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinte(
            $request->id_solicitud,
            $status
        );

        if ($solicitudes == 0) {
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => $request->status]);
        }
    }

    public function updateAllStatus(Request $request)
    {
        $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitud($request, 7);
    }

    //update aprobar solicitudes administrador de obra
    public function updateStatusAprobadorCC(Request $request)
    {
        $update = $this->repository->update(
            $request->id,
            $request->except(['id_solicitud'])
        );

        if (env('STATUS_APROBADO') == $request->status) {
            $this->updateStatusSolicitud($request, 7);
        }

        //enviar correos de estado
        return $update;
    }

    public function updateAllStatusAprobadorCC(Request $request)
    {
        $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitud($request, 7);
    }

    //update aprobar solicitudes RRHH
    public function updateStatusAprobadorRrhh(Request $request)
    {
        $update = $this->repository->update(
            $request->id,
            $request->except(['id_solicitud'])
        );

        $this->updateStatusSolicitudRrhh($request);
        //enviar correo desaprobacion con comentario
        return $update;
    }

    public function updateAllStatusAprobadorRrhh(Request $request)
    {
        $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitudRrhh($request);
    }

    public function updateStatusSolicitudRrhh($request)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinte(
            $request->id_solicitud,
            5
        );
        if ($solicitudes == 0) {
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => $request->status]);
        }
    }
}
