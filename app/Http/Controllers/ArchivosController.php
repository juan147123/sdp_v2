<?php

namespace App\Http\Controllers;

use App\Interfaces\ArchivoRepositoryInterface;
use Illuminate\Http\Request;

class ArchivosController extends Controller
{
    private $repository;

    public function __construct(ArchivoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function delete($id)
    {
        $this->repository->update($id, ["enable" => 0]);
    }
}
