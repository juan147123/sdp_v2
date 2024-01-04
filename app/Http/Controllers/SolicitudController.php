<?php

namespace App\Http\Controllers;

use App\Interfaces\SolicitudRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SolicitudController extends Controller
{
    private $repository;

    public function __construct(SolicitudRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function redirectPage(){
        return Inertia::render('Solicitud/Index');
    } 

    public function listAll(){
        return $this->repository->all(['*'], ['solicitudColaborador', 'solicitudColaborador.archivos', 'solicitudColaborador.SapMaestroCausalesTerminos', 'solicitudColaborador.checkAreaColaboradores']);
    }
}
