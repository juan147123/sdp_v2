<?php

namespace App\Http\Controllers;

use App\Interfaces\AplicacionUsuarioRepositoryInterface;
use App\Interfaces\UsuarioRolRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AplicacionUsuarioController extends Controller
{
    private $repository;
    private $repositoryRolUsuario;

    public function __construct(AplicacionUsuarioRepositoryInterface $repository,    UsuarioRolRepositoryInterface $repositoryRolUsuario,)
    {
        $this->repositoryRolUsuario = $repositoryRolUsuario;
        $this->repository = $repository;
    }


    public function redirectUsers()
    {
        return Inertia::render('Usuarios/Index');
    }

    public function listAll()
    {
        return $this->repository->listAllActive();
    }

    public function create(Request $request)
    {
        $data_user = array(
            "id_aplicacion" => $_ENV['ID_APLICACION'],
            "username" => $request->username,
            "name" => $request->name,
            "estado_sesion" => 1,
            "fecha_ini" => date('Y-m-d')
        );
        $new_user = $this->repository->create($data_user);

        $data_rol = array(
            "id_aplicacion_usuario" => $new_user->id_aplicacion_usuario,
            'id_rol' => $_ENV['USUARIO_SDP'],
            'fecha_ini' => date('Y-m-d'),
            "objeto_permitido" => $_ENV['ADMRRHH'],
        );
        $this->repositoryRolUsuario->create($data_rol);

        return redirect()->route('redirect.usuarios');
    }

    public function update(Request $request)
    {
        $this->repository->update($request->id_aplicacion_usuario, $request->all());
        return redirect()->route('redirect.usuarios');
    }

    public function delete(Request $request)
    {
        return $this->repository->update($request->id, ["estado_sesion" => 0]);
    }
}
