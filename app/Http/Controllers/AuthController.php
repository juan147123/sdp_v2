<?php

namespace App\Http\Controllers;

use App\Interfaces\{
    AplicacionUsuarioRepositoryInterface,
    PersonalChileRepositoryInterface,
    PersonalPeruRepositoryInterface,
    UsuarioRepositoryInterface,
    UsuarioRolRepositoryInterface
};
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private $repository;
    private $repositoryUsuario;
    private $repositoryRolUsuario;
    private $repositoryPersonalPE;
    private $repositoryPersonalCL;

    public function __construct(
        AplicacionUsuarioRepositoryInterface $repository,
        UsuarioRepositoryInterface $repositoryUsuario,
        UsuarioRolRepositoryInterface $repositoryRolUsuario,
        PersonalPeruRepositoryInterface $repositoryPersonalPE,
        PersonalChileRepositoryInterface $repositoryPersonalCL

    ) {
        $this->repository = $repository;
        $this->repositoryUsuario = $repositoryUsuario;
        $this->repositoryRolUsuario = $repositoryRolUsuario;
        $this->repositoryPersonalPE = $repositoryPersonalPE;
        $this->repositoryPersonalCL = $repositoryPersonalCL;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function getUser()
    {
        $user = Socialite::driver('google')->user();
        $email = $user->getEmail();

        // if ($email == 'serodriguez@flesan.com.pe') {
        //     $email = 'dcollas@flesan.com.pe';
        if ($email == 'jmestanza@flesan.com.pe') {
            // $email = 'dcollas@flesan.com.pe';
            // $email = 'frida.morales@flesan.cl';
            // $email = 'jorge.barrozo@flesan.cl';
            $email = 'acandia@flesan.cl';
            //$email = 'mmatamoros@flesan.com.pe';
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
        $AuthUser = null;
        $redirect = "";

        // otengo el usuario de la cuenta de google
        $usuario = $this->getUser();

        //busco el usuario en la tabla interna de usuarios 
       
        $usuario_bd = $this->repositoryUsuario->findByEmail($usuario['username']);
        //si existe ejecuto las validaciones correspondientes
        if ($usuario_bd) {
            $AuthUser = $this->validateTableUser($usuario, $usuario_bd);
        } else {
            $pais =  $usuario['pais'];
            if ($pais == 'PE') {
                $AuthUser = $this->validateLideresPE($usuario);
                $redirect = 'redirect.solicitud';
            } else {
                $AuthUser = $this->validateLideresObraPlantaCL($usuario);
                $redirect = 'redirect.colaboradores.cl';
            }
        }
        if (!$usuario_bd) {
            $rol = $_ENV['ADMINISTRADOR_LIDER'];
        } else {
            $rol = $usuario_bd['rol'];
        }

        if ($AuthUser) {
            Auth::login($AuthUser['appUser']);
            if ($rol == $_ENV['ADMINISTRADOR_LIDER']) {
                $np_lider  = $this->getNpLider();
                session(['np_lider' => $np_lider]);
            }
            return redirect()->route($redirect);
        } else {
            return $this->redirectToLogin();
        }
    }
    private function getNpLider()
    {
        $email = strtoupper(Auth::user()->username);
        $personalCl = $this->repositoryPersonalCL->getNpLiderByEmail($email);
        $personalPe = $this->repositoryPersonalPE->UserFindByEmail(Auth::user()->username);
        return $personalCl ? $personalCl : $personalPe->dni;
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

    public function validateLideresPE($usuario)
    {
        $redirect = 'colaboradores.peru';
        $datosLider =  $this->repositoryPersonalPE->UserFindByEmail($usuario['username']);
        $dniLideres = $this->repositoryPersonalPE->getDniLideres();

        if ($datosLider && in_array($datosLider->dni, $dniLideres)) {
            //Busca el usuario en seguridad app
            $appUser = $this->repository->findUserByEmail($usuario['username']);

            //Si no existe lo cre $this->updateAvatarName($appUser, $usuario);a
            if (!$appUser) {
                $appUser = $this->createAppUser($usuario);
                $userRol = $this->dtoAppUserRol(
                    $appUser->id_aplicacion_usuario,
                    $_ENV['ADMINISTRADOR_LIDER']
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
    }

    public function validateLideresObraPlantaCL($usuario)
    {
        $user = null;

        $correoLiderObra = $this->repositoryPersonalCL->getLideresObraCl();
        if (in_array($usuario['username'], $correoLiderObra)) {
            $user = $this->validateLideresCL($usuario);
        } else {
            $user = $this->validateLiderChile($usuario);
        }
        return $user;
    }

    public function validateLideresCL($usuario)
    {
        $redirect = 'colaboradores';
        //Busca el usuario en seguridad app
        $appUser = $this->repository->findUserByEmail($usuario['username']);

        //Si no existe lo crea
        if (!$appUser) {
            $appUser = $this->createAppUser($usuario);
            $userRol = $this->dtoAppUserRol(
                $appUser->id_aplicacion_usuario,
                $_ENV['ADMINISTRADOR_LIDER']
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

    public function validateLiderChile($usuario)
    {
        /* busca usuarios lideres de chile*/
        $userDwChile = $this->repositoryPersonalCL->UserFindByEmail(strtoupper($usuario['username']));
        $npLideres = $this->repositoryPersonalCL->getNpLideres();

        if ($userDwChile && in_array($userDwChile->user_id, $npLideres)) {
            $appUser = $this->repository->findUserByEmail($usuario['username']);
            //Si no existe lo crea
            if (!$appUser) {
                $appUser = $this->createAppUser($usuario);
                $userRol = $this->dtoAppUserRol(
                    $appUser->id_aplicacion_usuario,
                    $_ENV['ADMINISTRADOR_LIDER']
                );
                $this->createRolUser($userRol);
            }
            $redirect = 'colaboradores';
            return array(
                "appUser" => $appUser,
                "redirect" => $redirect
            );
        }
    }

    public function redirectToLogin()
    {
        return Inertia::render('Auth/Login', [
            'canLogin' => Route::has('login'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'autorizado' => 1,
        ]);
    }
}
