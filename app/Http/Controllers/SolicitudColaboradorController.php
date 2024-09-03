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


    public function delete($id, $id_solicitud)
    {

        $this->repository->update($id, ["enable" => 0]);
        $detalle =  $this->repository->all(['*'], [], ['id_solicitud' => $id_solicitud, "enable" => 1])->count();
        if ($detalle == 0) {
            $this->repositorySolicitud->update($id_solicitud, ["enable" => 0]);
        }
    }

    public function updateStatus(Request $request)
    {
        $this->repository->update(
            $request->id,
            $request->except(['id_solicitud'])
        );

        $this->updateStatusSolicitud($request, null);

        return redirect()->route('redirect.solicitud');
    }



    /* OBSERBADO */
    public function updateAllStatus(Request $request)
    {
        // $this->repository->updateStatusMasive($request->status, $request->ids);
        $this->updateStatusSolicitud($request, 7);
    }

    //update aprobar solicitudes administrador de obra
    public function updateStatusAprobadorCC(Request $request)
    {
        $update = $this->repository->update(
            $request->id,
            [
                "aprobado_administrador_obra" => $request->status,
                "comentario_admin_obra" => $request->comentario_admin_obra,
            ]
        );

        $this->updateStatusSolicitud($request, 2);

        //enviar correos de estado
        return $update;
    }

    //update aprobar solicitudes administrador de obra
    public function updateStatusAprobadorVisitador(Request $request)
    {
        $update = $this->repository->update(
            $request->id,
            [
                "aprobado_visitador_obra" => $request->status,
                "comentario_visitador" => $request->comentario_visitador,
            ]
        );

        $this->updateStatusSolicitud($request, 3);

        //enviar correos de estado
        return $update;
    }

    //update aprobar solicitudes RRHH
    public function updateStatusAprobadorRrhh(Request $request)
    {
        $update = $this->repository->update(
            $request->id,
            [
                "aprobado_rrhh" => $request->status,
                "comentario_rrhh" => $request->comentario_rrhh,
            ]
        );

        $this->updateStatusSolicitudRrhh($request, 4);
        //enviar correo desaprobacion con comentario
        return $update;
    }



    /* APROBACION MASIVA  ADMINISTRADOR DE OBRA*/

    public function updateAllStatusAprobadorCC(Request $request)
    {
        $this->repository->updateStatusMasiveAdmObr($request);
        $this->updateStatusSolicitud($request, null);
    }

    public function updateStatusSolicitud($request, $status)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinteAdmObr(
            $request->id_solicitud,
            $status
        );

        $solicitudes_aprobadas = $this->repository->getSolicitudColaboradorPendinteAdmObr(
            $request->id_solicitud,
            6
        );

        if ($solicitudes == 0) {
            $nuevoStatus = ($solicitudes_aprobadas != 0) ? 2 : 5;
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => $nuevoStatus]);
        }
    }

    /* APROBACION MASIVA VISITADOR DE OBRA */

    public function updateAllStatusVisitador(Request $request)
    {
        $this->repository->updateStatusMasiveVisiObr($request);
        $this->updateStatusSolicitudVisitador($request, null);
    }
    public function updateStatusSolicitudVisitador($request, $status)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinteVisiObr(
            $request->id_solicitud,
            $status
        );

        $solicitudes_aprobadas = $this->repository->getSolicitudColaboradorPendinteVisiObr(
            $request->id_solicitud,
            6
        );

        if ($solicitudes == 0) {
            $nuevoStatus = ($solicitudes_aprobadas != 0) ? 3 : 5;
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => $nuevoStatus]);
        }
    }


    /* APROBACION MASIVA RRHH */

    public function updateAllStatusAprobadorRrhh(Request $request)
    {
        $this->repository->updateStatusMasiveRrhh($request->status, $request->ids);
        $this->updateStatusSolicitudRrhh($request, null);
    }

    public function updateStatusSolicitudRrhh($request, $status)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinteRrhh(
            $request->id_solicitud,
            $status
        );

        $solicitudes_aprobadas = $this->repository->getSolicitudColaboradorPendinteRrhh(
            $request->id_solicitud,
            6
        );

        if ($solicitudes == 0) {
            $nuevoStatus = ($solicitudes_aprobadas != 0) ? 4 : 5;
            $this->repositorySolicitud->update($request->id_solicitud, ["status" => $nuevoStatus]);
        }
    }
}
