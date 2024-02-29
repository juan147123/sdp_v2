<?php

namespace App\Http\Controllers;

use App\Repositories\PersonalChileRepository;
use Illuminate\Http\Request;
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

    public function getColaboradoresCl()
    {
        $np_lider = session('np_lider');
        return $this->repository->getDataByUserIdLider($np_lider);
    }
}
