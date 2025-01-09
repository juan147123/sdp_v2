<?php

namespace App\Http\Controllers;

use App\Interfaces\ComentariosRepositoryInterface;
use Illuminate\Http\Request;

class ComentariosController extends Controller
{
    private $repository;

    public function __construct(ComentariosRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function listAll()
    {
        return $this->repository->all();
    }
  
    public function lisByIdConcat($id)
    {
        return $this->repository->all()->where('id_solicitud',$id);
    }

    public function create(Request $request){
        return $this->repository->create($request->all());
    }
    
    public function update($id,Request $request){
        return $this->repository->update($id,$request->all());
    }

}
