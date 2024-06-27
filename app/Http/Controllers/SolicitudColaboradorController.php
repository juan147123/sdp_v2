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

    public function updateStatus(Request $request)
    {
        $this->repository->update(
            $request->id,
            $request->except(['id_solicitud'])
        );

        $this->updateStatusSolicitud($request);

        return redirect()->route('redirect.solicitud');
    }
    public function updateStatusSolicitud($request)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinte(
            $request->id_solicitud
        );
        if ($solicitudes == 0) {
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => $request->status]);
        }
    }

    public function updateAllStatus(Request $request)
    {
        $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitud($request);
    }

    //update aprobar solicitudes cc
    public function updateStatusAprobadorCC(Request $request)
    {
        $this->repository->update(
            $request->id,
            $request->except(['id_solicitud'])
        );

        $this->updateStatusSolicitud($request);
        //enviar correo desaprobacion con comentario
        return redirect()->route('redirect.solicitud.aprobar');
    }

    public function updateAllStatusAprobadorCC(Request $request)
    {
        $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitud($request);
    }

    //update aprobar solicitudes cc
    public function updateStatusAprobadorRrhh(Request $request)
    {
        $this->repository->update(
            $request->id,
            $request->except(['id_solicitud'])
        );

        $this->updateStatusSolicitudRrhh($request);
        //enviar correo desaprobacion con comentario
        return redirect()->route('redirect.solicitud.aprobar');
    }

    public function updateAllStatusAprobadorRrhh(Request $request)
    {
        $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitudRrhh($request);
    }

    public function updateStatusSolicitudRrhh($request)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinte(
            $request->id_solicitud
        );
        if ($solicitudes == 0) {
            if ($request->status == 4) {
                $request->status = 5;
            } else if ($request->status == 2) {
                $request->status = 4;
            }
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => $request->status]);
        }
    }
}
