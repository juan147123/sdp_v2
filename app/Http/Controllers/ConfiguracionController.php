<?php

namespace App\Http\Controllers;

use App\Interfaces\ConfiguracionRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfiguracionController extends Controller
{
  private $repository;

  public function __construct(ConfiguracionRepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  public function listAllArea()
  {
    return $this->repository->all(['*'], ['Configuracion'])->where('categoria', 'area');
  }

  public function listAll()
  {
    $data = $this->repository->listAll();
    return Inertia::render('Configuraciones/Index')->with('data', $data);
  }
  
  public function create(Request $request)
  {
      $this->repository->create($request->all());
      return redirect('configuraciones/areas');
  }
  public function update(Request $request)
  {
      $this->repository->update($request->id, $request->all());
      return redirect('configuraciones/areas');
  }
  public function delete(Request $request)
  {
      $this->repository->update($request->id, ["enable" => 0]);
      return redirect('configuraciones/areas');
  }
}
