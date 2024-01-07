<?php

namespace App\Http\Controllers;

use App\Interfaces\ConfiguracionRepositoryInterface;
use Illuminate\Http\Request;

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
}
