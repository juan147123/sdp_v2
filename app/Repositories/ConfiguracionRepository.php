<?php

namespace App\Repositories;

use App\Models\Configuracion;
use App\Interfaces\ConfiguracionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ConfiguracionRepository extends BaseRepository implements
    ConfiguracionRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    private $repositoryAppUsu;
    private $repositoryUsu;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Configuracion $model, AplicacionUsuarioRepository $repositoryAppUsu, UsuarioRepository $repositoryUsu)
    {
        $this->model = $model;
        $this->repositoryAppUsu = $repositoryAppUsu;
        $this->repositoryUsu = $repositoryUsu;
    }

    public function listAll()
    {
        $areas_checklist = $this->model
            ->where('categoria', 'area')
            ->where('enable', 1)
            ->get()
            ->map(function ($area_checklist) {
                $checklist = $this->model
                    ->where('parent_id', $area_checklist->id)
                    ->where('enable', 1)
                    ->get();
                $area_checklist->checklist = $checklist;

                $usuarios = $this->getUsersByArea($area_checklist->id);
                $area_checklist->usuarios = $usuarios;
                return $area_checklist;
            });
        return $areas_checklist;
    }

    public function getUsersByArea($id)
    {
        $emails_area =  $this->repositoryUsu->findByIdArea($id);
        return $emails_area;
    }
    
    public function listByUser($id)
    {
        $areas_checklist = $this->model
            ->where('categoria', 'area')
            ->where('enable', 1)
            ->where('id', $id)
            ->get()
            ->map(function ($area_checklist) {
                $checklist = $this->model
                    ->where('parent_id', $area_checklist->id)
                    ->where('enable', 1)
                    ->get();
                $area_checklist->checklist = $checklist;
                return $area_checklist;
            });
        return $areas_checklist;
    }
}
