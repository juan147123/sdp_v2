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
}
