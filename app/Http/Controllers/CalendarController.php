<?php

namespace App\Http\Controllers;

use App\Interfaces\CalendarRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    private $repository;

    public function __construct(CalendarRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    public function redirectPage()
    {
        return Inertia::render('Calendar/CalendarPage');    
    }

    public function listAll()
    {
        return $this->repository->all();
    }

    public function create(Request $request)
    {
        return $this->repository->create($request->all());
    }
    
    public function update($id,Request $request)
    {
        return $this->repository->update($id,$request->all());
    }
    
    public function delete(Request $request)
    {
        return $this->repository->update($request->id, ["enable" => 0]);
    }
}
