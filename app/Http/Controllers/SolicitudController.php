<?php

namespace App\Http\Controllers;

use App\Exports\SolicitudExport;
use App\Interfaces\PersonalChileRepositoryInterface;
use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use App\Interfaces\SolicitudRepositoryInterface;
use App\Interfaces\UsuarioRolRepositoryInterface;
use App\Models\Archivos;
use App\Repositories\ArchivoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class SolicitudController extends Controller
{
    private $repository;
    private $archivoRepository;
    private $repositoryUsuarioRol;
    private $repositorySolicitudDetalle;
    private $extraService;
    private $personalChileRepository;

    public function __construct(
        SolicitudRepositoryInterface $repository,
        UsuarioRolRepositoryInterface $repositoryUsuarioRol,
        SolicitudColaboradorRepositoryInterface $repositorySolicitudDetalle,
        ExtraServicecontroller $extraService,
        ArchivoRepository $archivoRepository,
        PersonalChileRepositoryInterface $personalChileRepository
    ) {
        $this->repository = $repository;
        $this->repositoryUsuarioRol = $repositoryUsuarioRol;
        $this->repositorySolicitudDetalle = $repositorySolicitudDetalle;
        $this->extraService = $extraService;
        $this->archivoRepository = $archivoRepository;
        $this->personalChileRepository = $personalChileRepository;
    }

    //LISTADO DE SOLICITUDES 
    public function redirectPage()
    {
        return Inertia::render('Solicitud/Index');
    }

    // REDIRECCION A LA PAGINA DE SOLICITUDES DE OBRA
    public function redirectPageSolicitudObra()
    {
        return Inertia::render('SolicitudObra/Index');
    }

    //LISTADO DE SOLICITUDES POR PERMISOS (MEJORAR)
    public function listAll()
    {
        if (in_array('LIDERCL', session('objeto_permitido')) || in_array('LIDERPE', session('objeto_permitido')) || in_array('LIDEROBRACL', session('objeto_permitido'))) {
            $list = $this->listSolicitudesLider(['user_created' => strtoupper(Auth::user()->username), "enable" => 1]);
        } else {
            $list = $this->listSolicitudesLiderAll(["enable" => 1]);
        }
        return $list;
    }


    // MÓDULO DE APROBACION ADMINISTRADOR OBRA
    public function redirectPageSolicitudObraAprobarCC()
    {
        return Inertia::render('AprobacionSolicitudObra/Index');
    }

    public function listAllCCAprobar()
    {
        $centros_permitidos = [];
        if (!in_array('SUPERAD', session('objeto_permitido'))) {
            $aprobador = $this->personalChileRepository->getAprobadorObraCL(Auth::user()->username);
            $centros_permitidos = array("centro_costo" => explode(',', trim($aprobador->cc, '{}')));
            if (Auth::user()->username == 'cristian.donoso@flesan.cl') {
                $centros_permitidos['centro_costo'][] = 'CFMCFM020014';
            }
        }

        //CFMR10005CFM
        $result = $this->repository->all(['*'], [
            'estado',
            'archivos',
            'solicitudColaborador',
            'solicitudColaborador.archivos',
            'solicitudColaborador.estado',
            'solicitudColaborador.SapMaestroCausalesTerminos',
            'solicitudColaborador.checkAreaColaboradores',
            'solicitudColaborador.estadoadmin'
        ], [], $centros_permitidos)->whereIn('status', [1, 2]);

        return $result->values();
    }

    // MÓDULO DE APROBACION ADMINISTRADOR OBRA
    public function redirectPageSolicitudVisitadorAprobar()
    {
        return Inertia::render('AprobacionSolicitudVisitador/Index');
    }

    public function listAllVisitadorAprobar()
    {
        $centros_permitidos = [];
        if (!in_array('SUPERAD', session('objeto_permitido'))) {
            $aprobador = $this->personalChileRepository->getVisitadorObraCL(Auth::user()->username);
            $centros_permitidos = array("centro_costo" => explode(',', trim($aprobador->cc, '{}')));
            if (Auth::user()->username == 'cristian.donoso@flesan.cl') {
                $centros_permitidos['centro_costo'][] = 'CFMCFM020014';
                $centros_permitidos['centro_costo'][] = 'DVCR80010';
            }
            if (Auth::user()->username == 'jorge.stuardo@dvc.cl') {
                $centros_permitidos['centro_costo'][] = 'DMOPR12118GG';
            }
        }
        $result = $this->repository->all(['*'], [
            'estado',
            'archivos',
            'solicitudColaborador',
            'solicitudColaborador.archivos',
            'solicitudColaborador.estado',
            'solicitudColaborador.estadoadmin',
            'solicitudColaborador.estadovisitador',
            'solicitudColaborador.SapMaestroCausalesTerminos',
            'solicitudColaborador.checkAreaColaboradores'
        ], [], $centros_permitidos)->whereIn('status', [2, 3]);

        return $result->values();
    }


    // LISTADO DE SOLICITUDES POR LIDER
    public function listSolicitudesLider($conditionals)
    {
        return $this->repository->all(['*'], [
            'estado',
            'archivos',
            'solicitudColaborador2',
            'solicitudColaborador2.estado',
            'solicitudColaborador2.archivos',
            'solicitudColaborador2.SapMaestroCausalesTerminos',
            'solicitudColaborador2.checkAreaColaboradores',
            'solicitudColaborador2.estadoadmin',
            'solicitudColaborador2.estadovisitador',
            'solicitudColaborador2.estadorrhh',
        ], $conditionals);
    }

    public function listSolicitudesLiderAll($conditionals)
    {
        return $this->repository->all(['*'], [
            'estado',
            'archivos',
            'solicitudColaborador',
            'solicitudColaborador.estado',
            'solicitudColaborador.archivos',
            'solicitudColaborador.SapMaestroCausalesTerminos',
            'solicitudColaborador.checkAreaColaboradores',
            'solicitudColaborador.estadoadmin',
            'solicitudColaborador.estadovisitador',
            'solicitudColaborador.estadorrhh',
        ], $conditionals);
    }

    // CREACIÓN DE SOLICITUD UNITARIA Y/O MULTIPLE 
    public function createSolicitudMultiple($request)
    {
        $userCreated = strtoupper(Auth::user()->username);
        $np_lider = session('np_lider');
        $solicitud = $this->buildSolicitud($np_lider, $userCreated, $request);
        $new_solicitud = $this->repository->create($solicitud);
        return $new_solicitud;
    }

    public function createSolicitudDetail($request, $new_solicitud)
    {
        $solicitud_detail = $this->buildSolicitudDetail($request->all(), $new_solicitud);
        $newSolicitudDetail =  $this->repositorySolicitudDetalle->create($solicitud_detail);
        return $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud, $request->file('filesForm'), '', '', null);
    }

    private function buildSolicitud($npLider, $userCreated, $request)
    {
        return [
            'np_lider' => $npLider,
            'user_created' => $userCreated,
            'user_created_name' => strtoupper(Auth::user()->name),
            'centro_costo' => $request->centro_costo0,
            'full_ceco' => $request->full_ceco0,
            'rut_empresa' => $request->rut_empresa0,
            'razon_social' => $request->razon_social0,
            'obra' => $request->obra,
            'status' => $request->obra == 0 ? 3 : 1,
        ];
    }
    private function buildSolicitudDetail($solicitudcolaborador, $new_solicitud)
    {
        return [
            'user_id' => $solicitudcolaborador['user_id'],
            'nombre_completo' => $solicitudcolaborador['nombre_completo'],
            'motivo' => $solicitudcolaborador['motivoForm'],
            'fecha_desvinculacion' => $solicitudcolaborador['fechaForm'],
            'redireccion' => $solicitudcolaborador['redireccionForm'],
            'id_solicitud' => $new_solicitud->id,
            "rut_empresa" => $solicitudcolaborador["rut_empresa"],
            "centro_costo" => $solicitudcolaborador["centro_costo"],
        ];
    }
    public function createMultiple(Request $request)
    {

        $new_solicitud = $this->createSolicitudMultiple($request);
        $requestData = $request->all();
        $groupedIds = [];

        $archivos_variante = $request->file("variable0");
        $this->saveDocumentLocal(null, $new_solicitud,  $archivos_variante, "variable", "Variable", $new_solicitud->id);

        foreach ($requestData as $key => $value) {

            if (strpos($key, 'user_id') !== false) {
                $groupId = substr($key, -1);
                if (!in_array($groupId, $groupedIds)) {
                    $groupedIds[] = $groupId;
                }
            }
        }
        foreach ($groupedIds as $index => $value) {
            $data = array(
                "user_id" => $request->{"user_id$index"},
                "nombre_completo" => $request->{"nombre_completo$index"},
                "motivo" => $request->{"motivo$index"}['externalcode'],
                "fecha_desvinculacion" => $request->{"fecha_desvinculacion$index"},
                "redireccion" => $request->{"redireccion$index"},
                "rut_empresa" => $request->{"rut_empresa$index"},
                "centro_costo" => $request->{"centro_costo$index"},
                "full_ceco" => $request->{"full_ceco$index"},
                "fecha_ingreso" => $request->{"fecha_ingreso$index"},
                'id_solicitud' => $new_solicitud->id
            );
            $archivos1 = $request->file("carta_firmada$index");
            $archivos2 = $request->file("cese_dt$index");
            $archivos3 = $request->file("cese_afc$index");
            $archivos4 = $request->file("aporte_empleador$index");
            $archivos5 = $request->file("cert_defuncion$index");
            $archivos6 = $request->file("boleta_funebre$index");
            $archivos7 = $request->file("info_bancaria$index");
            $archivos8 = $request->file("convenio_practica$index");

            $newSolicitudDetail =  $this->repositorySolicitudDetalle->create($data);

            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos1, "carta_firmada", "Carta firmada o comprobante de envio por correo certificado", null);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos2, "cese_dt", "CESE DT", null);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos3, "cese_afc", "CESE AFC", null);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos4, "aporte_empleador", "Aporte empleador AFC", null);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos5, "cert_defuncion", "Certificado de defunción", null);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos6, "boleta_funebre", "Boleta o comprobante de gastos funebres", null);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos7, "info_bancaria", "Información bancaria del beneficiario", null);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos8, "convenio_practica", "Convenio de práctica", null);
        }



        $body = View::make('emails.NuevaSolicitud', [
            'data' => [
                'solicitud' => $new_solicitud,
                'linkAcceso' => 'qadesvinculaciones.grupoflesan.com',
                'usuario' => strtoupper(Auth::user()->name),
            ],
        ])->render();
        $emails_to = 'jmestanza@flesan.com.pe';

        $centro_costo = $new_solicitud->centro_costo;

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

        $subject = 'SISTEMA DE DESVINCULACIÓN SDP';
        ExtraServicecontroller::send_email_gf(
            $body,
            $subject,
            $emails_to
        );

        return redirect($request->pathname0);
    }

    public function updateMultiple(Request $request)
    {

        $requestData = $request->all();
        $old_solicitud = $this->repository->findById($request->id_solicitud0);
        $archivos_variante = $request->file("variable0");
        $this->saveDocumentLocal(null, $old_solicitud,  $archivos_variante, "variable", "Variable", $old_solicitud->id);

        $groupedIds = [];
        foreach ($requestData as $key => $value) {

            if (strpos($key, 'id') !== false) {
                $groupId = substr($key, -1);
                if (!in_array($groupId, $groupedIds)) {
                    $groupedIds[] = $groupId;
                }
            }
        }
        foreach ($groupedIds as $index => $value) {
            $data = array(
                "user_id" => $request->{"user_id$index"},
                "nombre_completo" => $request->{"nombre_completo$index"},
                "motivo" => $request->{"motivo$index"},
                "fecha_desvinculacion" => $request->{"fecha_desvinculacion$index"},
                "redireccion" => $request->{"redireccion$index"},
                "rut_empresa" => $request->{"rut_empresa$index"},
                "centro_costo" => $request->{"centro_costo$index"},
                "aprobado_administrador_obra" => $request->{"aprobado_administrador_obra$index"},
                "comentario_admin_obra" => null,
                "aprobado_visitador_obra" => $request->{"aprobado_visitador_obra$index"},
                "comentario_visitador" => null,
                "aprobado_rrhh" =>  $request->{"aprobado_rrhh$index"},
                "comentario_rrhh" => null,
            );

            $archivos1 = $request->file("carta_firmada$index");
            $archivos2 = $request->file("cese_dt$index");
            $archivos3 = $request->file("cese_afc$index");
            $archivos4 = $request->file("aporte_empleador$index");
            $archivos5 = $request->file("cert_defuncion$index");
            $archivos6 = $request->file("boleta_funebre$index");
            $archivos7 = $request->file("info_bancaria$index");
            $archivos8 = $request->file("convenio_practica$index");


            $newSolicitudDetail =  $this->repositorySolicitudDetalle->update($request->{"id$index"}, $data);

            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos1, "carta_firmada", "Carta firmada o comprobante de envio por correo certificado", null);
            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos2, "cese_dt", "CESE DT", null);
            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos3, "cese_afc", "CESE AFC", null);
            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos4, "aporte_empleador", "Aporte empleador AFC", null);
            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos5, "cert_defuncion", "Certificado de defunción", null);
            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos6, "boleta_funebre", "Boleta o comprobante de gastos funebres", null);
            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos7, "info_bancaria", "Información bancaria del beneficiario", null);
            $this->saveDocumentLocal($request->{"id$index"}, $old_solicitud,  $archivos8, "convenio_practica", "Convenio de práctica", null);
        }


        $admin = $request->aprobado_administrador_obra0;
        $visi = $request->aprobado_visitador_obra0;
        $rrhh = $request->aprobado_rrhh0;
        $status_soli = 1;

        if ($old_solicitud->obra == 0) {
            $status_soli = 3;
        } else {
            if ($admin == 7 || $admin == null) {
                $status_soli = 1;
            } else if (($visi == 7 || $visi == null) && $admin != null) {
                $status_soli = 2;
            } else if (($rrhh == 7 || $rrhh == null) && $visi != null) {
                $status_soli = 3;
            }
        }
        $this->repository->update($old_solicitud->id, ["status" => $status_soli]);

        return redirect($request->pathname0);
    }



    public function saveDocumentLocal($id, $new_solicitud, $archivos, $origen, $titulo, $id_solicitud)
    {
        try {
            if ($archivos) {
                $documents =  $this->archivoRepository->uploadFile($archivos, $id, $new_solicitud, $origen, $titulo, $id_solicitud);
                foreach ($documents as $document) {
                    $this->archivoRepository->create($document);
                }
            }
        } catch (\Throwable $th) {
            echo $th;
        }
    }
    // FIN DE CREACIÓN DE SOLICITUD UNITARIA Y/O MULTIPLE 



    //   TODO AREA PENDIENTE REVISIÓN

    public function redirectByArea()
    {
        return Inertia::render('ChecklistArea/Index');
    }

    public function listSolicitudesByArea()
    {
        return $this->repository->listSolicitudes();
    }

    //TODO RRHH PENIDENTE REVISIÓN
    public function redirectPageSolicitudRrhhAprobar()
    {
        return Inertia::render('AprobacionSolicitudRrhh/Index');
    }

    public function listAllAprobarRrhh()
    {

        $result = $this->repository->all(['*'], [
            'estado',
            'archivos',
            'solicitudColaborador',
            'solicitudColaborador.archivos',
            'solicitudColaborador.estado',
            'solicitudColaborador.estadoadmin',
            'solicitudColaborador.estadovisitador',
            'solicitudColaborador.estadorrhh',
            'solicitudColaborador.SapMaestroCausalesTerminos',
            'solicitudColaborador.checkAreaColaboradores'
        ])->whereIn('estado.id', [3, 4])->where("enable", 1);

        return $result->values();
    }

    public function export($fecha_inicio, $fecha_fin)
    {
        // Asegúrate de que las fechas estén en el formato correcto (Y-m-d)


        return Excel::download(new SolicitudExport($fecha_inicio, $fecha_fin, $this->repository), 'solicitudes_' . $fecha_inicio . '_a_' . $fecha_fin . '.xlsx');
    }
}
