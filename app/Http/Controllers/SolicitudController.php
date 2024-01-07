<?php

namespace App\Http\Controllers;

use App\Interfaces\SolicitudRepositoryInterface;
use App\Interfaces\UsuarioRolRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SolicitudController extends Controller
{
    private $repository;
    private $repositoryUsuarioRol;

    public function __construct(
        SolicitudRepositoryInterface $repository,
        UsuarioRolRepositoryInterface $repositoryUsuarioRol
    ) {
        $this->repository = $repository;
        $this->repositoryUsuarioRol = $repositoryUsuarioRol;
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
}
