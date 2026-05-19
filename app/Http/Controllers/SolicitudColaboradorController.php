<?php

namespace App\Http\Controllers;

use App\Interfaces\PersonalChileRepositoryInterface;
use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use App\Interfaces\SolicitudRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class SolicitudColaboradorController extends Controller
{
    private $repository;
    private $repositorySolicitud;
    private $repositoryPersonal;

    public function __construct(
        SolicitudColaboradorRepositoryInterface $repository,
        SolicitudRepositoryInterface $repositorySolicitud,
        PersonalChileRepositoryInterface $repositoryPersonal
    ) {
        $this->repository = $repository;
        $this->repositorySolicitud = $repositorySolicitud;
        $this->repositoryPersonal = $repositoryPersonal;
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

    public function updateStatusAprobadorCC(Request $request)
    {
        $solicitud_colaborador = $this->repository->findById($request->id);
        $solicitud = $this->repositorySolicitud->findById($solicitud_colaborador->id_solicitud);
        $cc = $solicitud->centro_costo ?? $solicitud_colaborador->centro_costo ?? null;

        $update = \DB::table('solicitud_colaborador')->where('id', $request->id)->update([
            'aprobado_administrador_obra' => $request->status,
            "comentario_admin_obra" => $request->comentario_admin_obra,
            "user_aprobate_admin_obra" => Auth::user()->name,
            "date_aprobate_admin_obra" => now()->toDateString(),
        ]);

        if ($update) {
            $bloqueados = ['rsalinas@flesan.cl', 'tchahuan@dvc.cl', 'jorge.stuardo@flesan.cl'];
            if ($cc) {
                // Buscar Aprobador 1 (Admin)
                $correoAprob1 = \DB::connection('dw_chile')
                    ->table('flesan_rrhh.sap_maestro_empresa_dep_un_cc as sme')
                    ->join('flesan_rrhh.sap_maestro_colaborador as smc', \DB::raw('sme.lider_departamento'), '=', \DB::raw('smc.user_id::text'))
                    ->where('sme.external_code_cc', $cc)
                    ->value('smc.correo_flesan');

                if ($correoAprob1) {
                    $correoAprob2 = \DB::connection('dw_chile')
                        ->table('flesan_rrhh.sap_maestro_colaborador as empleado')
                        ->join('flesan_rrhh.sap_maestro_colaborador as lider', function ($join) {
                            $join->on('empleado.np_lider', '=', \DB::raw('lider.user_id::text'));
                        })
                        ->leftJoin('flesan_rrhh.sap_maestro_empresa_dep_un_cc as emp', function ($join) {
                            $join->on('emp.lider_departamento', '=', \DB::raw('empleado.user_id::text'));
                        })
                        ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($correoAprob1)])
                        ->where('emp.external_code_cc', $cc)
                        ->value('lider.correo_flesan');

                    if ($correoAprob2 && in_array(strtolower(trim($correoAprob2)), $bloqueados)) {
                        \DB::table('solicitud_colaborador')->where('id', $request->id)->update([
                            "aprobado_visitador_obra" => 6,
                            "comentario_visitador" => "Aprobación automática (Directorio)",
                            "user_aprobate_visi_obra" => "SISTEMA",
                            "date_aprobate_visi_obra" => now()->toDateString(),
                        ]);

                        $request->merge(['status' => 6]);
                    }
                }
            }
        }

        $tieneVisiAprob = \DB::table('solicitud_colaborador')
            ->where('id_solicitud', $solicitud->id)
            ->where('aprobado_visitador_obra', 6)
            ->exists();

        if ($tieneVisiAprob) {
            $request->merge(['id_solicitud' => $solicitud->id]);
            $this->updateStatusSolicitudVisitador($request, null);
        } else {
            $solicitud_colaborador = $this->repository->findById($request->id);
            $this->sendMailStatus($solicitud, $solicitud_colaborador, $request->status, "administrador de obra");
            $request->merge(['id_solicitud' => $solicitud->id]);
            $this->updateStatusSolicitud($request, null);
        }
    }

    public function updateAllStatusAprobadorCC(Request $request)
    {
        $this->repository->updateStatusMasiveAdmObr($request);

        if ((int) $request->status === 6) {
            $bloqueados = ['rsalinas@flesan.cl', 'tchahuan@dvc.cl', 'jorge.stuardo@flesan.cl'];

            foreach ($request->ids as $id) {
                $colab = $this->repository->findById($id);
                $soli = $this->repositorySolicitud->findById($colab->id_solicitud);
                $cc = $soli->centro_costo ?? $colab->centro_costo ?? null;

                if ($cc) {
                    $correoAprob1 = \DB::connection('dw_chile')
                        ->table('flesan_rrhh.sap_maestro_empresa_dep_un_cc as sme')
                        ->join('flesan_rrhh.sap_maestro_colaborador as smc', \DB::raw('sme.lider_departamento'), '=', \DB::raw('smc.user_id::text'))
                        ->where('sme.external_code_cc', $cc)
                        ->value('smc.correo_flesan');

                    if ($correoAprob1) {
                        $correoAprob2 = \DB::connection('dw_chile')
                            ->table('flesan_rrhh.sap_maestro_colaborador as empleado')
                            ->join('flesan_rrhh.sap_maestro_colaborador as lider', function ($join) {
                                $join->on('empleado.np_lider', '=', \DB::raw('lider.user_id::text'));
                            })
                            ->leftJoin('flesan_rrhh.sap_maestro_empresa_dep_un_cc as emp', function ($join) {
                                $join->on('emp.lider_departamento', '=', \DB::raw('empleado.user_id::text'));
                            })
                            ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($correoAprob1)])
                            ->where('emp.external_code_cc', $cc)
                            ->value('lider.correo_flesan');

                        if ($correoAprob2 && in_array(strtolower(trim($correoAprob2)), $bloqueados)) {
                            \DB::table('solicitud_colaborador')->where('id', $id)->update([
                                "aprobado_visitador_obra" => 6,
                                "comentario_visitador" => "Aprobación automática (Directorio)",
                                "user_aprobate_visi_obra" => "SISTEMA",
                                "date_aprobate_visi_obra" => now()->toDateString(),
                            ]);
                        }
                    }
                }
            }
        }

        $tieneVisiAprob = \DB::table('solicitud_colaborador')
            ->where('id_solicitud', $request->id_solicitud)
            ->where('aprobado_visitador_obra', 6)
            ->exists();

        if ($tieneVisiAprob) {
            $this->updateStatusSolicitudVisitador($request, null);
        } else {
            $this->updateStatusSolicitud($request, null);
        }
    }

    public function updateStatusSolicitud($request, $status)
    {
        $solicitudes = \DB::table('solicitud_colaborador')
            ->where('id_solicitud', $request->id_solicitud)
            ->where('aprobado_administrador_obra', $status)
            ->count();

        $solicitudes_aprobadas = \DB::table('solicitud_colaborador')
            ->where('id_solicitud', $request->id_solicitud)
            ->where('aprobado_administrador_obra', 6)
            ->count();

        if ($solicitudes == 0) {
            $nuevoStatus = ($solicitudes_aprobadas != 0) ? 2 : 5;
            \DB::table('solicitudes')->where('id', $request->id_solicitud)->update(["status" => $nuevoStatus]);
            $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);
            $this->sendMailStatusMasive($solicitud, $nuevoStatus, "administrador de obra");
        }
    }

    public function updateAllStatusVisitador(Request $request)
    {
        $this->repository->updateStatusMasiveVisiObr($request);
        $this->updateStatusSolicitudVisitador($request, null);
    }

    public function updateStatusAprobadorVisitador(Request $request)
    {
        $update = $this->repository->update($request->id, [
            'aprobado_visitador_obra' => $request->status,
            'comentario_visitador' => $request->comentario_visitador,
            'user_aprobate_visi_obra' => Auth::user()->name,
            'date_aprobate_visi_obra' => now()->toDateString(),
        ]);

        $solicitud_colaborador = $this->repository->findById($request->id);
        $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);

        $this->sendMailStatus($solicitud, $solicitud_colaborador, $request->status, 'visitador de obra');
        $this->updateStatusSolicitudVisitador($request, null);

        return $update;
    }

    public function updateStatusAprobadorRrhh(Request $request)
    {
        $update = $this->repository->update($request->id, [
            'aprobado_rrhh' => $request->status,
            'comentario_rrhh' => $request->comentario_rrhh,
            'user_aprobate_rrhh_obra' => Auth::user()->name,
            'date_aprobate_rrhh_obra' => now()->toDateString(),
        ]);

        $solicitud_colaborador = $this->repository->findById($request->id);
        $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);

        $this->sendMailStatus($solicitud, $solicitud_colaborador, $request->status, 'rrhh');
        $this->updateStatusSolicitudRrhh($request, null);

        return $update;
    }

    public function updateAllStatusAprobadorRrhh(Request $request)
    {
        $this->repository->updateStatusMasiveRrhh($request);
        $this->updateStatusSolicitudRrhh($request, null);
    }

    public function updateStatusSolicitudRrhh($request, $status)
    {
        $solicitudes = $this->repository->getSolicitudColaboradorPendinteRrhh(
            $request->id_solicitud,
            $status
        );

        if ($request->obra != 0) {
            $solicitudes_aprobadas = $this->repository->getSolicitudColaboradorPendinteRrhh(
                $request->id_solicitud,
                6
            );
        } else {
            $solicitudes_aprobadas = $this->repository->getSolicitudColaboradorPendinteRrhhPlanta(
                $request->id_solicitud,
                6
            );
        }

        if ($solicitudes == 0) {
            $nuevoStatus = ($solicitudes_aprobadas != 0) ? 4 : 5;
            \DB::table('solicitudes')->where('id', $request->id_solicitud)->update(['status' => $nuevoStatus]);
            $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);
            $this->sendMailStatusMasive($solicitud, $nuevoStatus, 'rrhh');
        }
    }

    public function updateStatusSolicitudVisitador($request, $status)
    {
        $solicitudes = \DB::table('solicitud_colaborador')
            ->where('id_solicitud', $request->id_solicitud)
            ->where('aprobado_visitador_obra', $status)
            ->where('aprobado_administrador_obra', '!=', 7)
            ->count();

        $solicitudes_aprobadas = \DB::table('solicitud_colaborador')
            ->where('id_solicitud', $request->id_solicitud)
            ->where('aprobado_visitador_obra', 6)
            ->where('aprobado_administrador_obra', '!=', 7)
            ->count();

        if ($solicitudes == 0) {
            $nuevoStatus = ($solicitudes_aprobadas != 0) ? 3 : 5;
            \DB::table('solicitudes')->where('id', $request->id_solicitud)->update(["status" => $nuevoStatus]);
            $solicitud = $this->repositorySolicitud->findById($request->id_solicitud);
            $this->sendMailStatusMasive($solicitud, $nuevoStatus, "visitador de obra");
        }
    }

    public function sendMailStatus($solicitud, $solicitud_colaborador, $status, $rol)
    {
        $bloqueados = ['rsalinas@flesan.cl', 'tchahuan@dvc.cl', 'jorge.stuardo@flesan.cl'];

        $estado = [
            'descripcion' => ($status == 6) ? "Aprobada" : (($status == 7) ? "Rechazada" : "Pendiente"),
            'cabecera' => ($status == 6) ? "Aprobación de Solicitud" : (($status == 7) ? "Rechazo de Solicitud" : "Actualización de Solicitud")
        ];

        $comentarios = "";
        $rolNorm = mb_strtolower($rol);
        if ($rolNorm == "administrador de obra") {
            $comentarios = $solicitud_colaborador->comentario_admin_obra;
        } elseif ($rolNorm == "visitador de obra") {
            $comentarios = $solicitud_colaborador->comentario_visitador;
        } elseif ($rolNorm === 'rrhh' || $rolNorm === 'administrador de rrhh') {
            $comentarios = $solicitud_colaborador->comentario_rrhh;
        }

        $body = View::make('emails.NuevaSolicitud', [
            'data' => [
                'solicitud' => $solicitud,
                'estado_cabecera' => $estado['cabecera'],
                'estado_descripcion' => $estado['descripcion'],
                'linkAcceso' => 'https://desvinculaciones.grupoflesan.com/',
                'usuario' => strtoupper(Auth::user()->name),
                'colaborador' => $solicitud_colaborador,
                'comentarios' => $comentarios,
            ],
        ])->render();

        $destinatarios = [strtolower(trim($solicitud->user_created))];

        if ($status == 6 && $rolNorm == "administrador de obra") {
            $cc = $solicitud->centro_costo ?? $solicitud_colaborador->centro_costo ?? null;
            if ($cc) {
                $correoAprob1 = \DB::connection('dw_chile')
                    ->table('flesan_rrhh.sap_maestro_empresa_dep_un_cc as sme')
                    ->join('flesan_rrhh.sap_maestro_colaborador as smc', \DB::raw('sme.lider_departamento'), '=', \DB::raw('smc.user_id::text'))
                    ->where('sme.external_code_cc', $cc)
                    ->value('smc.correo_flesan');

                if ($correoAprob1) {
                    $correoAprob2 = \DB::connection('dw_chile')
                        ->table('flesan_rrhh.sap_maestro_colaborador as empleado')
                        ->join('flesan_rrhh.sap_maestro_colaborador as lider', function ($join) {
                            $join->on('empleado.np_lider', '=', \DB::raw('lider.user_id::text'));
                        })
                        ->leftJoin('flesan_rrhh.sap_maestro_empresa_dep_un_cc as emp', function ($join) {
                            $join->on('emp.lider_departamento', '=', \DB::raw('empleado.user_id::text'));
                        })
                        ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($correoAprob1)])
                        ->where('emp.external_code_cc', $cc)
                        ->value('lider.correo_flesan');

                    if ($correoAprob2) {
                        $destinatarios[] = strtolower(trim($correoAprob2));
                    }
                }
            }
        }

        $destinatarios = array_unique(array_filter($destinatarios));
        $destinatarios = array_filter($destinatarios, function ($email) use ($bloqueados) {
            return !in_array(strtolower(trim($email)), $bloqueados);
        });

        if (count($destinatarios) > 0) {
            $emails_to = implode(',', $destinatarios);
            $subject = $estado['cabecera'] . " " . $solicitud->codigo;
            ExtraServicecontroller::send_email_gf($body, $subject, $emails_to, 'https://i-c-flesan.github.io/assets-flesan/headers_aplicativos/header_rojo_sdd_nuevasolicitud.png');
        }
    }

    public function sendMailStatusMasive($solicitud, $status, $rol)
    {
        $bloqueados = ['rsalinas@flesan.cl', 'tchahuan@dvc.cl', 'jorge.stuardo@flesan.cl'];

        $estado = [
            'descripcion' => ($status == 2 || $status == 3 || $status == 4 || $status == 6) ? "Aprobada" : (($status == 5 || $status == 7) ? "Rechazada" : "Pendiente"),
            'cabecera' => ($status == 2 || $status == 3 || $status == 4 || $status == 6) ? "Aprobación de Solicitud" : (($status == 5 || $status == 7) ? "Rechazo de Solicitud" : "Actualización de Solicitud")
        ];

        $colaboradores = \DB::table('solicitud_colaborador')
            ->where('id_solicitud', $solicitud->id)
            ->where('enable', 1)
            ->get();

        // Formatear nombres para el correo (evita el JSON crudo)
        $nombresColaboradores = $colaboradores->pluck('nombre_completo')->implode(', ');

        $comentarios = $colaboradores->map(function ($colab) use ($rol) {
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

        $body = View::make('emails.SolicitudColaboradorEstadoMultiple', [
            'data' => [
                'solicitud' => $solicitud,
                'estado_cabecera' => $estado['cabecera'],
                'estado_descripcion' => $estado['descripcion'],
                'linkAcceso' => 'https://desvinculaciones.grupoflesan.com/',
                'usuario' => strtoupper(Auth::user()->name),
                'colaboradores' => $nombresColaboradores,
                'comentarios' => $comentarios,
            ],
        ])->render();

        $destinatarios = [strtolower(trim($solicitud->user_created))];
        $rolNorm = mb_strtolower($rol);

        if (($status == 2 || $status == 6) && $rolNorm === 'administrador de obra') {
            $cc = $solicitud->centro_costo ?? null;
            if ($cc) {
                $correoAprob1 = \DB::connection('dw_chile')
                    ->table('flesan_rrhh.sap_maestro_empresa_dep_un_cc as sme')
                    ->join('flesan_rrhh.sap_maestro_colaborador as smc', \DB::raw('sme.lider_departamento'), '=', \DB::raw('smc.user_id::text'))
                    ->where('sme.external_code_cc', $cc)
                    ->value('smc.correo_flesan');

                if ($correoAprob1) {
                    $correoAprob2 = \DB::connection('dw_chile')
                        ->table('flesan_rrhh.sap_maestro_colaborador as empleado')
                        ->join('flesan_rrhh.sap_maestro_colaborador as lider', function ($join) {
                            $join->on('empleado.np_lider', '=', \DB::raw('lider.user_id::text'));
                        })
                        ->leftJoin('flesan_rrhh.sap_maestro_empresa_dep_un_cc as emp', function ($join) {
                            $join->on('emp.lider_departamento', '=', \DB::raw('empleado.user_id::text'));
                        })
                        ->whereRaw('LOWER(empleado.correo_flesan) = ?', [strtolower($correoAprob1)])
                        ->where('emp.external_code_cc', $cc)
                        ->value('lider.correo_flesan');

                    if ($correoAprob2) {
                        $destinatarios[] = strtolower(trim($correoAprob2));
                    }
                }
            }
        }

        $destinatarios = array_unique(array_filter($destinatarios));
        $destinatarios = array_filter($destinatarios, function ($email) use ($bloqueados) {
            return !in_array(strtolower(trim($email)), $bloqueados);
        });

        if (count($destinatarios) > 0) {
            $emails_to = implode(',', $destinatarios);
            $subject = $estado['cabecera'] . " " . $solicitud->codigo;
            ExtraServicecontroller::send_email_gf($body, $subject, $emails_to, 'https://i-c-flesan.github.io/assets-flesan/headers_aplicativos/header_rojo_sdd_nuevasolicitud.png');
        }
    }
}
