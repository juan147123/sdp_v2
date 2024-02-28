<?php

namespace App\Http\Controllers;

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

    public function __construct(
        SolicitudRepositoryInterface $repository,
        UsuarioRolRepositoryInterface $repositoryUsuarioRol,
        SolicitudColaboradorRepositoryInterface $repositorySolicitudDetalle,
        ExtraServicecontroller $extraService,
        ArchivoRepository $archivoRepository
    ) {
        $this->repository = $repository;
        $this->repositoryUsuarioRol = $repositoryUsuarioRol;
        $this->repositorySolicitudDetalle = $repositorySolicitudDetalle;
        $this->extraService = $extraService;
        $this->archivoRepository = $archivoRepository;
    }

    public function redirectPage()
    {
        return Inertia::render('Solicitud/Index');
    }

    public function listAll()
    {
        $usuario_rol = $this->repositoryUsuarioRol->getIdRol();
        $list = [];
        $idlideres = [env('ADMINISTRADOR_LIDER'), env('ADMINISTRADOR_LIDER_OBRA')];

        if (in_array($usuario_rol['id_rol'], $idlideres)) {
            $list = $this->listSolicitudesLider(['user_created' => strtoupper(Auth::user()->username)]);
        } else {
            $list = $this->listSolicitudesLider([]);
        }
        return $list;
    }

    public function listSolicitudesLider($conditionals)
    {
        return $this->repository->all(['*'], ['solicitudColaborador', 'solicitudColaborador.archivos', 'solicitudColaborador.SapMaestroCausalesTerminos', 'solicitudColaborador.checkAreaColaboradores'], $conditionals);
    }

    public function create(Request $request)
    {
        $new_solicitud = $this->createSolicitud();
        $new_detail = $this->createSolicitudDetail($request, $new_solicitud);
        return 1;
    }

    public function createSolicitud()
    {
        $userCreated = strtoupper(Auth::user()->username);
        $np_lider = session('np_lider');
        $solicitud = $this->buildSolicitud($np_lider, $userCreated);
        $new_solicitud = $this->repository->create($solicitud);
        return $new_solicitud;
    }

    public function createSolicitudDetail($request, $new_solicitud)
    {
        $solicitud_detail = $this->buildSolicitudDetail($request->all(), $new_solicitud);
        $newSolicitudDetail =  $this->repositorySolicitudDetalle->create($solicitud_detail);
        return $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud, $request->file('filesForm'));
    }

    private function buildSolicitud($npLider, $userCreated)
    {
        return [
            'np_lider' => $npLider,
            'user_created' => $userCreated,
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
            'id_solicitud' => $new_solicitud->id
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
        $new_solicitud = $this->createSolicitud();
        $requestData = $request->all();
        $groupedIds = [];

        foreach ($requestData as $key => $value) {
            $gruopId = substr($key, -1);
            if (!in_array($gruopId, $groupedIds)) {
                $groupedIds[] = $gruopId;
            }
        }

        foreach ($groupedIds as $index => $value) {
            $data = array(
                "user_id" => $request->{"userId$index"},
                "nombre_completo" => $request->{"nombreCompleto$index"},
                "motivo" => $request->{"motivo$index"},
                "fecha_desvinculacion" => $request->{"fechaMotivo$index"},
                "redireccion" => $request->{"redireccion$index"},
                'id_solicitud' => 1
            );
            $archivos = $request->file("archivos$index");
            $newSolicitudDetail =  $this->repositorySolicitudDetalle->create($data);
            $this->saveDocumentLocal($newSolicitudDetail->id, $new_solicitud,  $archivos);
        }
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
}
