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
            // $email = 'fabian.castro@flesan.cl';
            $email = 'marcos.gallardo@flesan.cl';
            // $email = 'julian.lopez@flesan.cl';
            // $email = 'cristian.donoso@flesan.cl';
            // $email = 'felisa.castillo@flesan.cl';
            // $email = 'carlos.hermosilla@flesan.cl';
            // $email = 'fernanda.stanic@flesan.cl';
            // $email = 'catalina.fuentes@flesan.cl';
            
        }

        if ($email == 'bruno.cuevas@flesan.cl') {
            $email = 'marcos.gallardo@flesan.cl';
        }


        if ($email == 'cecilia.silva@flesan.cl') {  //solicitante DMOPR12118GG 
            $email = 'fabian.castro@flesan.cl';
        }
        
        if ($email == 'gabriel.fernandez@flesan.cl') {  //solicitante DMOPR12118GG 
            $email = 'fabian.castro@flesan.cl';
        }

        if ($email == 'lorena.faray@flesan.cl') {  //SOLICITANTE de Obra CFMR10005CFM  
            $email = 'marcos.gallardo@flesan.cl';
        }


        if ($email == 'carolina.carreno@flesan.cl') {  //administrador de Obra DMOPR12118GG 
            $email = 'julian.lopez@flesan.cl';
        }

        if ($email == 'carolina.zavala@flesan.cl') {  //visitador de obra DMOPR12118GG 
            $email = 'cristian.donoso@flesan.cl';
        }



            
        /* DMOPR8110PU */

        if ($email == 'cristobal.figueroa@flesan.cl') {  //SOLICITANTE de Obra DMOPR8110PU  
            $email = 'felisa.castillo@flesan.cl';
        }
  
        if ($email == 'nicolas.toro@flesan.cl') {  //administrador de Obra DMOPR8110PU  
            $email = 'carlos.hermosilla@flesan.cl';
        }
       
        if ($email == 'cesar.munoz@flesan.cl') {  //visitador de Obra DMOPR8110PU  
            $email = 'fernanda.stanic@flesan.cl';
        }
        if ($email == 'alejandro.candia@flesan.cl') {  //visitador de Obra DMOPR8110PU  
            $email = 'fernanda.stanic@flesan.cl';
        }


        /* APROBADORES RRHH */
        if ($email == 'catalina.fuentes@flesan.cl') { // aprobador rrhh
            $email = 'catalina.fuentes@flesan.cl';
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

        $np_lider  = $this->getNpLider($usuario_mail);
        if(!$np_lider){
            return $this->redirectToLogin();
        }
        if ($usuario_seguridad_app) {
            Auth::login($usuario_seguridad_app);
            $rol = $this->repositoryRolUsuario->getDataRol($usuario_seguridad_app->id_aplicacion_usuario);
            $objeto_permitido = explode(",", $rol->objeto_permitido);
            session(['np_lider' => $np_lider]);
            session(['objeto_permitido' => $objeto_permitido]);
            return $this->setRedirect($objeto_permitido);
        } else {


            $usuario_bd = $this->repositoryUsuario->findByEmail($usuario_mail);

            if ($usuario_bd && env("ADMINISTRADOR_RRHH") == $usuario_bd->rol) array_push($permisos, 'ADMRRHH');
            if ($usuario_bd && env("ADMINISTRADOR_AREA") == $usuario_bd->rol) array_push($permisos, 'ADMAREA');
            if ($usuario_bd && env("SUPER_ADMINISTRADOR") == $usuario_bd->rol) array_push($permisos, 'SUPERAD');

 /*            $personalCl = $this->repositoryPersonalCL->getNpLiderByEmail($usuario_mail);
            $personalPe = $this->repositoryPersonalPE->UserFindByEmail($usuario_mail);

            if ($personalCl) array_push($permisos, 'LIDERCL');
            if ($personalPe) array_push($permisos, 'LIDERPE'); */

            $liderObracl = $this->repositoryPersonalCL->getLiderObraCl($usuario_mail);
            if ($liderObracl) array_push($permisos, 'LIDEROBRACL');

            $aprobadorObra = $this->repositoryPersonalCL->getAdministradorDepartamento($usuario_mail);
            if ($aprobadorObra) array_push($permisos, 'APROBOBRA');

            $aprobadorVisitador = $this->repositoryPersonalCL->getVisitadorDepartamento($usuario_mail);
            if ($aprobadorVisitador) array_push($permisos, 'APROBVISITADOR');

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
                $np_lider  = $this->getNpLider($usuario_mail);
                $objeto_permitido = explode(",", implode(",", $permisos));
                session(['np_lider' => $np_lider]);
                session(['objeto_permitido' => $objeto_permitido]);
                return $this->setRedirect($objeto_permitido);
            } else {
                return $this->redirectToLogin();
            }
        }
    }
    private function getNpLider($email)
    {
        $email = strtoupper($email);
        $personalCl = $this->repositoryPersonalCL->getNpLiderByEmail($email);
        $personalPe = $this->repositoryPersonalPE->UserFindByEmail($email);
        return $personalCl !== null ? $personalCl : ($personalPe->dni ?? null);
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
    public function setRedirect($objeto_permitido)
    {
        $redirect = "";
        if (in_array(env("LIDERCL"), $objeto_permitido) && !in_array(env("LIDEROBRACL"), $objeto_permitido)) {
            $redirect = 'redirect.colaboradores.cl';
        } else if (in_array(env("LIDERPE"), $objeto_permitido)) {
            $redirect = 'redirect.colaboradores.pe';
        } else if (in_array(env("LIDEROBRACL"), $objeto_permitido)) {
            $redirect = 'redirect.colaboradores.obra.cl';
        } else if (in_array(env("APROBOBRA"), $objeto_permitido)) {
            $redirect = 'redirect.solicitud.aprobar';
        } else if (in_array(env("ADMRRHH"), $objeto_permitido)) {
            $redirect = 'redirect.solicitud.rrhh';
        } else if (in_array(env("SUPERAD"), $objeto_permitido)) {
            $redirect = 'redirect.configuraciones';
        } else if (in_array(env("ADMAREA"), $objeto_permitido)) {
            $redirect = 'redirect.solicitud.area';
        } else if (in_array(env("APROBVISITADOR"), $objeto_permitido)) {
            $redirect = 'redirect.solicitud.visitador.aprobar';
        }
        return redirect()->route($redirect);
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
