<?php

namespace App\Http\Controllers;

use App\Interfaces\AplicacionUsuarioRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AplicacionUsuarioController extends Controller
{
    private $repository;

    public function __construct(AplicacionUsuarioRepositoryInterface $repository)
    {
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
    
    public function delete(Request $request)
    {
        return $this->repository->update($request->id, ["estado_sesion" => 0]);
    }
}
