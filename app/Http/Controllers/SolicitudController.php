<?php

namespace App\Http\Controllers;

use App\Interfaces\SolicitudRepositoryInterface;
use App\Interfaces\UsuarioRolRepositoryInterface;
use Illuminate\Http\Request;
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
        $list = $this->listSolicitudesLider();
        if ($usuario_rol['idrol'] == env('ADMINISTRADOR_LIDER')) {
        }
        return $list;
    }
    public function listSolicitudesLider()
    {
        return $this->repository->all(['*'], ['solicitudColaborador', 'solicitudColaborador.archivos', 'solicitudColaborador.SapMaestroCausalesTerminos', 'solicitudColaborador.checkAreaColaboradores']);
    }
}
