<?php

namespace App\Http\Controllers;

use App\Interfaces\UsuarioRepositoryInterface;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    private $repository;

    public function __construct(UsuarioRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    public function create(Request $request)
    {
        $this->repository->create($request->all());
        return redirect()->route('redirect.configuraciones');
    }


    public function update(Request $request)
    {
        $this->repository->update($request->id, $request->all());
        return redirect()->route('redirect.configuraciones');
    }

    public function delete(Request $request)
    {
        $this->repository->update($request->id, ["enable" => 0]);
        return redirect()->route('redirect.configuraciones');
    }
}
