<?php

namespace App\Http\Controllers;

use App\Interfaces\SolicitudRepositoryInterface;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    private $repository;

    public function __construct(SolicitudRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function listAll(){
        return $this->repository->all(['*'], ['solicitudColaborador', 'solicitudColaborador.archivos', 'solicitudColaborador.SapMaestroCausalesTerminos', 'solicitudColaborador.checkAreaColaboradores']);
    }
}
