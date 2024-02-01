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
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => 3]);
        }
    }

    public function updateAllStatus(Request $request)
    {
        $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitud($request);
    }
}
