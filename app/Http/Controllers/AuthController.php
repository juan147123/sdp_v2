<?php

namespace App\Http\Controllers;

use App\Interfaces\AplicacionUsuarioRepositoryInterface;
use App\Interfaces\UsuarioRepositoryInterface;
use App\Interfaces\UsuarioRolRepositoryInterface;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private $repository;
    private $repositoryUsuario;
    private $repositoryRolUsuario;

    public function __construct(
        AplicacionUsuarioRepositoryInterface $repository,
        UsuarioRepositoryInterface $repositoryUsuario,
        UsuarioRolRepositoryInterface $repositoryRolUsuario,

    ) {
        $this->repository = $repository;
        $this->repositoryUsuario = $repositoryUsuario;
        $this->repositoryRolUsuario = $repositoryRolUsuario;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function getUser()
    {
        $user = Socialite::driver('google')->user();
        $email = $user->getEmail();
        
        if($email == 'jmestanza@flesan.com.pe'){
            $email = 'frida.morales@flesan.cl';
        }

        $extension_correo = substr($email, -2);
        $pais = "PE";
        if ($extension_correo == 'cl') {
            $pais = 'CL';
        }
        return array(
            "username" => $email,
            'name' => $user->getName(),
            'provider_id' => $user->getId(),
            'avatar' => $user->getAvatar(),
            'provider' => 'GOOGLE',
            'estado_sesion' => 1,
            'fecha_ini' => date('Y-m-d'),
            'id_aplicacion' => $_ENV['ID_APLICACION'],
            'pais' => $pais
        );
    }
    public function handleGoogleCallback()
    {
        // otengo el usuario de la cuenta de google
        $usuario = $this->getUser();

        //busco el usuario en la tabla interna de usuarios 
        $usuario_bd = $this->repositoryUsuario->findByEmail($usuario['username']);
        //si existe ejecuto las validaciones correspondientes
        if ($usuario_bd) {
            $AuthUser = $this->validateTableUser($usuario, $usuario_bd);
            dd($AuthUser);
        }else{
            dd('usuario lider');
            $usuario['pais'];
        }
    }

    public function validateTableUser($usuario, $usuario_bd)
    {
        //Busca la ruta segun rol
        $redirect = $this->setRedirect($usuario_bd->rol, $usuario_bd->pais);

        //Busca el usuario en seguridad app
        $appUser = $this->repository->findUserByEmail($usuario['username']);

        //Si no exist lo crea
        if (!$appUser) {
            $appUser = $this->createAppUser($usuario);
            $userRol = $this->dtoAppUserRol(
                $appUser->id_aplicacion_usuario,
                $usuario_bd['rol']
            );
            $this->createRolUser($userRol);
        }

        //Si existe actuaiza su nombre y avatar
        $this->updateAvatarName($appUser, $usuario);

        // Retorna el usuario a loguear más su ruta de redireccion
        return array(
            "appUser" => $appUser,
            "redirect" => $redirect
        );
    }

    public function dtoAppUserRol($id, $rol)
    {
        $data = [
            'id_aplicacion_usuario' => $id,
            'id_rol' => $rol,
            'fecha_ini' => date('Y-m-d'),
        ];
        return $data;
    }

    public function createAppUser($data)
    {
        return $this->repository->create($data);
    }

    public function createRolUser($data)
    {
        return $this->repositoryRolUsuario->create($data);
    }

    public function updateAvatarName($appUser, $usuario)
    {
        $this->repository->update($appUser->id_aplicacion_usuario, ["name" => $usuario['name'], "avatar" => $usuario['avatar']]);
    }
    public function setRedirect($rol, $pais)
    {
        $redirect = "";
        if ($rol == env('ADMINISTRADOR_AREA')) {
            $redirect = 'solicitud.area';
        } else if ($rol == env('SUPER_ADMINISTRADOR')) {
            $redirect = 'solicitudes';
        } else if ($rol == env('ADMINISTRADOR_LIDER') && $pais == "PE") {
            $redirect = 'colaboradores.peru';
        } else if ($rol == env('ADMINISTRADOR_LIDER')) {
            $redirect = 'colaboradores';
        } else if ($rol == env('ADMINISTRADOR_LIDER_OBRA')) {
            $redirect = 'colaboradores';
        } else if ($rol == env('ADMINISTRADOR_RRHH')) {
            $redirect = 'solicitudes';
        }
        return  $redirect;
    }

    public function validateLideresPE(){
       
    }
}
