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
            // $email = 'abeckdorf@flesan.com.pe';
            // $email = 'frida.morales@flesan.cl';
            // $email = 'sebastian.valck@flesan.cl';
            // $email = 'jorge.barrozo@flesan.cl';
            $email = 'acandia@flesan.cl';
            // $email = 'SEBASTIAN.VALCK@FLESAN.CL';
            // $email = 'mmatamoros@flesan.com.pe';
            // $email = 'ESM@FLESAN.CL';
            // $email = 'jorge.fernandezdelrio@flesan.cl';
            // $email = 'david.vilugron@flesan.cl'; //pendiente
            // $email = 'cesar.munoz@flesan.cl';
            // $email = 'jonathan.gaete@dvc.cl';
            // $email = 'serodriguez@flesan.com.pe';
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

        $usuario = $this->getUser();
        $usuario_mail = $usuario['username'];
        $permisos = [];
        $usuario_seguridad_app = $this->repository->findUserByEmail($usuario_mail);

        if ($usuario_seguridad_app) {
            Auth::login($usuario_seguridad_app);
            $rol = $this->repositoryRolUsuario->getDataRol($usuario_seguridad_app->id_aplicacion_usuario);
            $np_lider  = $this->getNpLider();
            session(['np_lider' => $np_lider]);
            session(['objeto_permitido' => explode(",", $rol->objeto_permitido)]);
            return redirect()->route("redirect.colaboradores.cl");
        } else {


            $usuario_bd = $this->repositoryUsuario->findByEmail($usuario_mail);

            if ($usuario_bd && env("ADMINISTRADOR_RRHH") == $usuario_bd->rol) array_push($permisos, 'ADMRRHH');
            if ($usuario_bd && env("ADMINISTRADOR_AREA") == $usuario_bd->rol) array_push($permisos, 'ADMAREA');
            if ($usuario_bd && env("SUPER_ADMINISTRADOR") == $usuario_bd->rol) array_push($permisos, 'SUPERAD');

            $personalCl = $this->repositoryPersonalCL->getNpLiderByEmail($usuario_mail);
            $personalPe = $this->repositoryPersonalPE->UserFindByEmail($usuario_mail);


            if ($personalCl) array_push($permisos, 'LIDERCL');
            if ($personalPe) array_push($permisos, 'LIDERPE');

            $liderObracl = $this->repositoryPersonalCL->getLiderObraCl($usuario_mail);
            if ($liderObracl) array_push($permisos, 'LIDEROBRACL');

            $aprobadorObra = $this->repositoryPersonalCL->getAdministradorDepartamento($usuario_mail);
            if ($aprobadorObra) array_push($permisos, 'APROBOBRA');

            if ($permisos) {
                $appUser = $this->createAppUser($usuario);

                $data = [
                    'id_aplicacion_usuario' =>  $appUser->id_aplicacion_usuario,
                    'id_rol' => $_ENV['USUARIO_SDP'],
                    'fecha_ini' => date('Y-m-d'),
                    'objeto_permitido' =>  implode(",", $permisos)
                ];

                $this->createRolUser($data);
                Auth::login($appUser);
                $np_lider  = $this->getNpLider();
                session(['np_lider' => $np_lider]);
                session(['objeto_permitido' => explode(",", implode(",", $permisos))]);
                return redirect()->route("redirect.colaboradores.cl");
            } else {
                return $this->redirectToLogin();
            }
        }
    }
    private function getNpLider()
    {
        $email = strtoupper(Auth::user()->username);
        $personalCl = $this->repositoryPersonalCL->getNpLiderByEmail($email);
        $personalPe = $this->repositoryPersonalPE->UserFindByEmail(Auth::user()->username);
        return $personalCl ? $personalCl : $personalPe->dni;
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
            $redirect = 'redirect.solicitud.area';
        } else if ($rol == env('ADMINISTRADOR_LIDER') && $pais == "PE") {
            $redirect = 'redirect.colaboradores.pe';
        } else if ($rol == env('ADMINISTRADOR_LIDER') && $pais == "CL") {
            $redirect = 'redirect.colaboradores.cl';
        } else if ($rol == env('ADMINISTRADOR_LIDER_OBRA')) {
            $redirect = 'redirect.colaboradores.obra.cl';
        } else if ($rol == env('ADMINISTRADOR_RRHH')) {
            $redirect = 'solicitudes';
        } else if ($rol == env('SUPER_ADMINISTRADOR')) {
            $redirect = 'solicitudes';
        }
        return  $redirect;
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
