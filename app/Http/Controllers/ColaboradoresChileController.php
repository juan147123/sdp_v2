<?php

namespace App\Http\Controllers;

use App\Repositories\PersonalChileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ColaboradoresChileController extends Controller
{
    private $repository;

    public function __construct(
        PersonalChileRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function redirectPage()
    {
        return Inertia::render('ColaboradoresCl/Index');
    }
    
    public function redirectPageObraCl()
    {
        return Inertia::render('ColaboradoresObraCl/Index');
    }

    public function getColaboradoresCl()
    {
        $np_lider = session('np_lider');
        return $this->repository->getDataByUserIdLider($np_lider);
    }

    public function getColaboradoresObra()
    {
        $email = Auth::user()->username;
        dd($email);
        return $this->repository->getColaboradoresObra($email);
    }
}
