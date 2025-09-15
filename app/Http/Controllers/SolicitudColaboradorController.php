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
            $this->sendMailStatusMasive($solicitud, $nuevoStatus, "administrador de obra");
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
            $this->sendMailStatusMasive($solicitud, $nuevoStatus, "visitador de obra");
        }
    }


    /* APROBACION MASIVA RRHH */

    public function updateAllStatusAprobadorRrhh(Request $request)
    {
        $this->repository->updateStatusMasiveRrhh($request);
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
                $this->sendMailStatusMasive($solicitud, $nuevoStatus, "rrhh");
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
                $this->sendMailStatusMasive($solicitud, $nuevoStatus, "rrhh");
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

        $rolNorm = mb_strtolower($rol);
        $comentario = null;

        if ($rolNorm === 'administrador de obra') {
            $comentario = $solicitud_colaborador->comentario_admin_obra ?? null;
        } elseif ($rolNorm === 'visitador de obra') {
            $comentario = $solicitud_colaborador->comentario_visitador ?? null;
        } elseif ($rolNorm === 'rrhh' || $rolNorm === 'administrador de rrhh') {
            $comentario = $solicitud_colaborador->comentario_rrhh ?? null;
        }

        $body = View::make('emails.SolicitudColaboradorEstado', [
            'data' => [
                'solicitud' => $solicitud,
                'solicitud_colaborador' => $solicitud_colaborador,
                'estado_cabecera' => $estado['cabecera'],
                'estado_descripcion' => $estado['descripcion'],
                'rol_usuario' => $rol,
                'linkAcceso' => 'https://desvinculaciones.grupoflesan.com/',
                'usuario' => strtoupper(Auth::user()->name),
                'comentarios' => $comentario ?? 'Sin comentarios'
            ],
        ])->render();

        // siempre se noitifica al solicitante
        $to = [strtolower(trim($solicitud->user_created))];

            if ((int)$status === 6 && $rol) {
            $rolNorm = mb_strtolower($rol);

            if ($rolNorm === 'administrador de obra') {
                // Buscar aprobador 1
                $aprob1 = \DB::connection('dw_chile')
                    ->table('flesan_rrhh.sap_maestro_colaborador_1 as empleado')
                    ->join('flesan_rrhh.sap_maestro_colaborador_1 as lider', 'empleado.np_lider', '=', \DB::raw('lider.user_id::text'))
                    ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($solicitud->user_created)])
                    ->value('lider.correo_flesan');

                if ($aprob1) {
                    // Buscar aprobador 2 a partir del correo del aprobador 1
                    $aprob2 = \DB::connection('dw_chile')
                        ->table('flesan_rrhh.sap_maestro_colaborador_1 as empleado')
                        ->join('flesan_rrhh.sap_maestro_colaborador_1 as lider', 'empleado.np_lider', '=', \DB::raw('lider.user_id::text'))
                        ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($aprob1)])
                        ->value('lider.correo_flesan');

                    if ($aprob2) {
                        $to[] = strtolower(trim($aprob2));
                    }
                }

            } elseif ($rolNorm === 'visitador de obra') {
                // Notificar a RRHH
                $rrhh = \DB::connection('dw_seguridad_app')
                    ->table('seguridadapp.usuario_rol as ur')
                    ->join('seguridadapp.aplicacion_usuario as au', 'ur.id_aplicacion_usuario', '=', 'au.id_aplicacion_usuario')
                    ->whereIn('ur.objeto_permitido', ['ADMRRHH'])
                    ->pluck('au.username')
                    ->map(fn($e) => strtolower(trim($e)))
                    ->unique()
                    ->values()
                    ->toArray();

                $to = array_merge($to, $rrhh);
            }
        }

        $emails_to = implode(',', array_values(array_unique(array_filter($to))));
        $subject = "{$estado['descripcion']} DE COLABORADOR - SISTEMA DE DESVINCULACIÓN SDP";

        ExtraServicecontroller::send_email_gf($body, $subject, $emails_to);
    }
public function sendMailStatusMasive($solicitud, $status, $rol = null)
{
   $estados = [
            5 => ['cabecera' => 'RECHAZADA', 'descripcion' => 'RECHAZO'],
            'default' => ['cabecera' => 'APROBADA', 'descripcion' => 'APROBACIÓN']
        ];
    $estado = $estados[$status] ?? $estados['default'];

    // Cargar la solicitud con sus colaboradores
    $solicitud = \App\Models\Solicitud::with('solicitudColaborador')->find($solicitud->id);

    // Extraer nombres de colaboradores
    $colaboradores = $solicitud->solicitudColaborador
        ->pluck('nombre_completo')
        ->filter()
        ->implode(', ');

    // Extraer comentarios de colaboradores (dependiendo del rol)
    $comentarios = $solicitud->solicitudColaborador
        ->map(function ($colab) use ($rol) {
            $rolNorm = mb_strtolower($rol);

            if ($rolNorm === 'administrador de obra') {
                return $colab->comentario_admin_obra;
            } elseif ($rolNorm === 'visitador de obra') {
                return $colab->comentario_visitador;
            } elseif ($rolNorm === 'rrhh' || $rolNorm === 'administrador de rrhh') {
                return $colab->comentario_rrhh;
            }
            return null;
        })
        ->filter()
        ->first();

    // Construir el body del correo
    $body = View::make('emails.SolicitudColaboradorEstadoMultiple', [
        'data' => [
            'solicitud' => $solicitud,
            'estado_cabecera' => $estado['cabecera'],
            'estado_descripcion' => $estado['descripcion'],
            'linkAcceso' => 'https://desvinculaciones.grupoflesan.com/',
            'usuario' => strtoupper(Auth::user()->name),
            'colaboradores' => $colaboradores,
            'comentarios' => $comentarios,
        ],
    ])->render();

    // siempre partimos por el solicitante
    $to = [strtolower(trim($solicitud->user_created))];

    $rolNorm = $rol ? mb_strtolower($rol) : null;

    // Caso: aprobado por administrador de obra → notificar aprobador 2
    if ((int)$status === 2 && $rolNorm === 'administrador de obra') {
        $colaborador = $solicitud->solicitudColaborador->first();

        if ($colaborador) {
            $aprob1 = \DB::connection('dw_chile')
                ->table('flesan_rrhh.sap_maestro_colaborador_1 as empleado')
                ->join('flesan_rrhh.sap_maestro_colaborador_1 as lider', 'empleado.np_lider', '=', \DB::raw('lider.user_id::text'))
                ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($colaborador->correo_flesan ?? $solicitud->user_created)])
                ->value('lider.correo_flesan');

            if ($aprob1) {
                $aprob2 = \DB::connection('dw_chile')
                    ->table('flesan_rrhh.sap_maestro_colaborador_1 as empleado')
                    ->join('flesan_rrhh.sap_maestro_colaborador_1 as lider', 'empleado.np_lider', '=', \DB::raw('lider.user_id::text'))
                    ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($aprob1)])
                    ->value('lider.correo_flesan');

                if ($aprob2) {
                    $to[] = strtolower(trim($aprob2));
                }
            }
        }
    }

    // Caso: aprobado por visitador de obra → notificar a RRHH
    if ((int)$status === 3 && $rolNorm === 'visitador de obra') {
        $rrhh = \DB::connection('dw_seguridad_app')
            ->table('seguridadapp.usuario_rol as ur')
            ->join('seguridadapp.aplicacion_usuario as au', 'ur.id_aplicacion_usuario', '=', 'au.id_aplicacion_usuario')
            ->whereIn('ur.objeto_permitido', ['ADMRRHH'])
            ->pluck('au.username')
            ->map(fn($e) => strtolower(trim($e)))
            ->unique()
            ->values()
            ->toArray();

        $to = array_merge($to, $rrhh);
    }

    // Caso: rechazado (total o parcial)
    if (in_array((int)$status, [5, 7])) {
        // ya tenemos al solicitante en $to
        // aquí no agregamos más destinatarios
    }

    $emails_to = implode(',', array_values(array_unique(array_filter($to))));
    $subject = "{$estado['descripcion']} DE SOLICITUD MASIVA - SISTEMA DE DESVINCULACIÓN SDP";

    \Log::info("Correos enviados en MASIVO ({$rol}): " . $emails_to);

    ExtraServicecontroller::send_email_gf($body, $subject, $emails_to);
}
}
