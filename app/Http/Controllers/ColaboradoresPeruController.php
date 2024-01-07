<?php

namespace App\Http\Controllers;

use App\Interfaces\PersonalPeruRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ColaboradoresPeruController extends Controller
{

    private $repository;

    public function __construct(
        PersonalPeruRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    
    public function redirectPage()
    {
        return Inertia::render('ColaboradoresPe/Index');
    }

   
    public function getColaboradoresPe()
    {
        return $this->repository->getDataByEmailLider(Auth::user()->username);
    }
}
