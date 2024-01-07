<?php

namespace App\Http\Controllers;

use App\Interfaces\TerminosRepositoryInterface;
use Illuminate\Http\Request;

class TerminosController extends Controller
{
    private $repository;

    public function __construct(TerminosRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function listAll()
    {
        return $this->repository->all(["externalcode", "name"], [], ["status" => "A"]);
    }
}
