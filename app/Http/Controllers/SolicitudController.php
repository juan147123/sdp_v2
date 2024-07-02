<?php

namespace App\Http\Controllers;

use App\Interfaces\PersonalChileRepositoryInterface;
use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use App\Interfaces\SolicitudRepositoryInterface;
use App\Interfaces\UsuarioRolRepositoryInterface;
use App\Models\Archivos;
use App\Repositories\ArchivoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

    public function redirectPage()
    {
        return Inertia::render('Solicitud/Index');
    }

    public function redirectPageSolicitudObra()
    {
        return Inertia::render('SolicitudObra/Index');
    }

    public function listAll()
    {
        if (in_array('LIDERCL', session('objeto_permitido')) || in_array('LIDERPE', session('objeto_permitido')) || in_array('LIDEROBRACL', session('objeto_permitido'))) {
            $list = $this->listSolicitudesLider(['user_created' => strtoupper(Auth::user()->username)]);
        } else {
            $list = $this->listSolicitudesLider([]);
        }
        return $list;
    }

    public function redirectPageSolicitudObraAprobarCC()
    {
        return Inertia::render('AprobacionSolicitudObra/Index');
    }

    public function listAllCCAprobar()
    {
        $aprobador = $this->personalChileRepository->getAprobadorObraCL(Auth::user()->username);
        $centros_permitidos = explode(',', trim($aprobador->cc, '{}'));

        $result = $this->repository->all(['*'], [
            'estado',
            'solicitudColaborador',
            'solicitudColaborador.archivos',
            'solicitudColaborador.SapMaestroCausalesTerminos',
            'solicitudColaborador.checkAreaColaboradores'
        ])->whereIn('centro_costo', $centros_permitidos);

        return $result->values();
    }

    public function listSolicitudesLider($conditionals)
    {
        return $this->repository->all(['*'], ['estado', 'solicitudColaborador', 'solicitudColaborador.archivos', 'solicitudColaborador.SapMaestroCausalesTerminos', 'solicitudColaborador.checkAreaColaboradores'], $conditionals);
    }

    public function create(Request $request)
    {
        $new_solicitud = $this->createSolicitud($request);
        $new_detail = $this->createSolicitudDetail($request, $new_solicitud);
        return 'ok';
    }

    public function createSolicitud($request)
    {
        $userCreated = strtoupper(Auth::user()->username);
        $np_lider = session('np_lider');
        $solicitud = $this->buildSolicitud($np_lider, $userCreated, $request->centro_costo);
        $new_solicitud = $this->repository->create($solicitud);
        return $new_solicitud;
    }

    public function createSolicitudMultiple($request)
    {
        $userCreated = strtoupper(Auth::user()->username);
        $np_lider = session('np_lider');
        $solicitud = $this->buildSolicitud($np_lider, $userCreated, $request->centro_costo0);
        $new_solicitud = $this->repository->create($solicitud);
        return $new_solicitud;
    }

    public function createSolicitudDetail($request, $new_solicitud)
    {
        $solicitud_detail = $this->buildSolicitudDetail($request->all(), $new_solicitud);
        $newSolicitudDetail =  $this->repositorySolicitudDetalle->create($solicitud_detail);
        return $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud, $request->file('filesForm'));
    }

    private function buildSolicitud($npLider, $userCreated, $centro_costo)
    {
        return [
            'np_lider' => $npLider,
            'user_created' => $userCreated,
            'centro_costo' => $centro_costo,
            'obra' => session('obra') == null ? 0 : session('obra'),
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
    public function saveDocumentLocal($id, $new_solicitud, $archivos)
    {
        try {
            if ($archivos) {
                $documents =  $this->archivoRepository->uploadFile($archivos, $id, $new_solicitud);
                return Archivos::insert($documents);
            }
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    public function saveDocument($id_solicitud_colaborador, $request)
    {
        $archivos = $request->file('filesForm');
        if ($archivos) {
            $app = 'google_garantias_preliminares';
            $documentsInDrive =  $this->extraService->SendDocumentsInDrive($archivos, $app);
            $dataDocumentsInsert = $this->insertFiles($documentsInDrive, $id_solicitud_colaborador);
            Archivos::insert($dataDocumentsInsert);
        }
    }

    public function insertFiles($documentsInDrive, $id_solicitud_colaborador)
    {
        $archivosInsert = [];
        $archivosGuardados = $documentsInDrive;

        foreach ($archivosGuardados as $archivoGuardado) {
            $archivosInsert[] = self::dtoArchivos($archivoGuardado, $id_solicitud_colaborador);
        }
        return $archivosInsert;
    }

    public function dtoArchivos($document, $id_solicitud_colaborador)
    {
        $archivo = array(
            'id_solicitud_colaborador' => $id_solicitud_colaborador,
            'name' => $document['name'],
            'uuid_name' => $document['name_uid'],
            'extension' => $document['extension'],
            'path' => $document['url'],
        );

        return $archivo;
    }

    // registro multiple

    public function createMultiple(Request $request)
    {

        $new_solicitud = $this->createSolicitudMultiple($request);
        $requestData = $request->all();
        $groupedIds = [];
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
                'id_solicitud' => $new_solicitud->id
            );
            $archivos1 = $request->file("carta_firmada$index");
            $archivos2 = $request->file("cese_dt$index");
            $archivos3 = $request->file("cese_afc$index");
            $archivos4 = $request->file("aporte_empleador$index");
            $archivos5 = $request->file("cert_defuncion$index");
            $archivos6 = $request->file("boleta_funebre$index");
            $archivos7 = $request->file("info_bancaria$index");

            $newSolicitudDetail =  $this->repositorySolicitudDetalle->create($data);

            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos1);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos2);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos3);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos4);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos5);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos6);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos7);
        }

        return redirect($request->pathname0);
    }

    //   TODO AREA

    public function redirectByArea()
    {
        return Inertia::render('ChecklistArea/Index');
    }

    public function listSolicitudesByArea()
    {
        return $this->repository->listSolicitudes();
    }

    //TODO RRHH
    public function redirectPageSolicitudRrhhAprobar()
    {
        return Inertia::render('AprobacionSolicitudRrhh/Index');
    }

    public function listAllAprobarRrhh()
    {

        $result = $this->repository->all(['*'], [
            'estado',
            'solicitudColaborador',
            'solicitudColaborador.archivos',
            'solicitudColaborador.SapMaestroCausalesTerminos',
            'solicitudColaborador.checkAreaColaboradores'
        ])->whereIn('estado.id', [3, 5]);

        return $result->values();
    }
}
