<?php

namespace App\Http\Controllers;

use App\Interfaces\CheckColaboradorRepositoryInterface;
use App\Interfaces\UsuarioRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckColaboradorController extends Controller
{
    private $repository;
    protected $repousuario;
    public function __construct(
        CheckColaboradorRepositoryInterface $repository,
        UsuarioRepositoryInterface $repousuario
    ) {
        $this->repository = $repository;
        $this->repousuario = $repousuario;
    }

    public function listAll()
    {
        return $this->repository->all();
    }

    public function listAllBySolicitudArea($id_solicitud)
    {
        $usuario = Auth::user();
        $usuario_area = $this->repousuario->findByEmail(
            strval($usuario->username),
            env("ADMINISTRADOR_AREA")
        );
        return $this->repository->all()->where('area_id',$usuario_area->id_area)->where('id_solicitud',$id_solicitud)->first();
    }

    public function create(Request $request)
    {
        return $this->repository->create($request->all());
    }

    public function update($id, Request $request)
    {
        return $this->repository->update($id, $request->all());
    }

    public function delete(Request $request)
    {
        return $this->repository->update($request->id, ["enable" => 0]);
    }
}
