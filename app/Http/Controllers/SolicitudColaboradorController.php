<?php

namespace App\Http\Controllers;

use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use App\Interfaces\SolicitudRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
                "user_aprobate_admin_obra" => Auth::user()->name,
                "date_aprobate_admin_obra" => now()->toDateString(),

            ]
        );

        $solicitud_colaborador = $this->repository->findById($request->id);
        $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);

        $this->sendMailStatus($solicitud, $solicitud_colaborador, $request->status, "administrador de obra");

        $this->updateStatusSolicitud($request, null);

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
                "user_aprobate_visi_obra" => Auth::user()->name,
                "date_aprobate_visi_obra" => now()->toDateString(),
            ]
        );

        $solicitud_colaborador = $this->repository->findById($request->id);
        $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);

        $this->sendMailStatus($solicitud, $solicitud_colaborador, $request->status, "visitador de obra");

        $this->updateStatusSolicitudVisitador($request, null);

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
                "user_aprobate_rrhh_obra" => Auth::user()->name,
                "date_aprobate_rrhh_obra" => now()->toDateString(),
            ]
        );

        $solicitud_colaborador = $this->repository->findById($request->id);
        $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);

        $this->sendMailStatus($solicitud, $solicitud_colaborador, $request->status, "administrador de rrhh");

        $this->updateStatusSolicitudRrhh($request, null);
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
            $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);
            $this->sendMailStatusMasive($solicitud, $nuevoStatus);
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
            $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);
            $this->sendMailStatusMasive($solicitud, $nuevoStatus);
        }
    }


    /* APROBACION MASIVA RRHH */

    public function updateAllStatusAprobadorRrhh(Request $request)
    {
        // $this->repository->updateStatusMasiveRrhh($request);
        $this->updateStatusSolicitudRrhh($request, null);
    }

    public function updateStatusSolicitudRrhh($request, $status)
    {
        if ($request->obra != 0) {
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
                $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);
                $this->sendMailStatusMasive($solicitud, $nuevoStatus);
            }
        }else{
            $solicitudes = $this->repository->getSolicitudColaboradorPendinteRrhh(
                $request->id_solicitud,
                $status
            );

            $solicitudes_aprobadas = $this->repository->getSolicitudColaboradorPendinteRrhhPlanta(
                $request->id_solicitud,
                6
            );

            if ($solicitudes == 0) {
                $nuevoStatus = ($solicitudes_aprobadas != 0) ? 4 : 5;
                $this->repositorySolicitud->update($request->id_solicitud, ["status" => $nuevoStatus]);
                $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);
                $this->sendMailStatusMasive($solicitud, $nuevoStatus);
            }
        }
    }

    public function sendMailStatus($solicitud, $solicitud_colaborador, $status, $rol)
    {
        $estados = [
            6 => ['cabecera' => 'APROBADO(A)', 'descripcion' => 'APROBACIÓN'],
            'default' => ['cabecera' => 'RECHAZADO(A)', 'descripcion' => 'RECHAZO']
        ];

        $estado = $estados[$status] ?? $estados['default'];

        $body = View::make('emails.SolicitudColaboradorEstado', [
            'data' => [
                'solicitud' => $solicitud,
                'solicitud_colaborador' => $solicitud_colaborador,
                'estado_cabecera' => $estado['cabecera'],
                'estado_descripcion' => $estado['descripcion'],
                'rol_usuario' => $rol,
                'linkAcceso' => 'qadesvinculaciones.grupoflesan.com',
                'usuario' => strtoupper(Auth::user()->name),
            ],
        ])->render();

        $emails_to = 'jmestanza@flesan.com.pe';

        $centro_costo = $solicitud->centro_costo;
        /* 
        if ($centro_costo == 'DMOPR12118GG') {
            $emails_to .= ',gabriel.fernandez@flesan.cl';
            $emails_to .= ',cecilia.silva@flesan.cl';
            $emails_to .= ',carolina.carreno@flesan.cl';
            $emails_to .= ',carolina.zavala@flesan.cl';
            $emails_to .= ',catalina.fuentes@flesan.cl';
        } 
        else if ($centro_costo == 'CFMR10005CFM') {
            $emails_to .= ',lorena.faray@flesan.cl';
        }
        else if ($centro_costo == 'DMRM1052DEM') {
            $emails_to .= ',cristobal.figueroa@flesan.cl';
            $emails_to .= ',nicolas.toro@flesan.cl';
            $emails_to .= ',david.vilugron@flesan.cl';
            $emails_to .= ',catalina.fuentes@flesan.cl';
        } */

        $subject = "{$estado['descripcion']} DE COLABORADOR - SISTEMA DE DESVINCULACIÓN SDP";

        ExtraServicecontroller::send_email_gf(
            $body,
            $subject,
            $emails_to
        );
    }
    public function sendMailStatusMasive($solicitud, $status)
    {
        $estados = [
            5 => ['cabecera' => 'RECHAZADA', 'descripcion' => 'RECHAZO'],
            'default' => ['cabecera' => 'APROBADA', 'descripcion' => 'APROBACIÓN']
        ];

        $estado = $estados[$status] ?? $estados['default'];

        $body = View::make('emails.SolicitudColaboradorEstadoMultiple', [
            'data' => [
                'solicitud' => $solicitud,
                'estado_cabecera' => $estado['cabecera'],
                'estado_descripcion' => $estado['descripcion'],
                'linkAcceso' => 'qadesvinculaciones.grupoflesan.com'
            ],
        ])->render();

        $emails_to = 'jmestanza@flesan.com.pe';


        $centro_costo = $solicitud->centro_costo;

        if ($centro_costo == 'DMOPR12118GG') {
            $emails_to .= ',cecilia.silva@flesan.cl';
            $emails_to .= ',david.vilugron@flesan.cl';
            $emails_to .= ',carolina.carreno@flesan.cl';
            $emails_to .= ',carolina.zavala@flesan.cl';
            $emails_to .= ',catalina.fuentes@flesan.cl';
        } else if ($centro_costo == 'CFMCFM020014') {
            $emails_to .= ',cristobal.figueroa@flesan.cl';
            $emails_to .= ',nicolas.toro@flesan.cl';
            $emails_to .= ',carolina.carreno@flesan.cl';
            $emails_to .= ',carolina.zavala@flesan.cl';
            $emails_to .= ',catalina.fuentes@flesan.cl';
        } else if ($centro_costo == 'DVCR80010') {
            $emails_to .= ',lorena.faray@flesan.cl';
            $emails_to .= ',maria.cayuqueo@flesan.cl';
            $emails_to .= ',carolina.carreno@flesan.cl';
            $emails_to .= ',carolina.zavala@flesan.cl';
            $emails_to .= ',catalina.fuentes@flesan.cl';
        }

        $subject = "{$estado['descripcion']} DE SOLICITUD - SISTEMA DE DESVINCULACIÓN SDP";

        ExtraServicecontroller::send_email_gf(
            $body,
            $subject,
            $emails_to
        );
    }
}
