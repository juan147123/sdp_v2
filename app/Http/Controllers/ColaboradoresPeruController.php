<?php

namespace App\Http\Controllers;

use App\Interfaces\PersonalPeruRepositoryInterface;
use Illuminate\Http\Request;

class ColaboradoresPeruController extends Controller
{

    private $repository;

    public function __construct(
        PersonalPeruRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    
    public function validateLideresPE($email)
    {
        return $this->repository->getDataByEmailLider($email);
    }
}
