<?php

namespace App\Repositories;

use App\Models\{Solicitud};
use App\Interfaces\{SolicitudRepositoryInterface, UsuarioRepositoryInterface};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudRepository extends BaseRepository implements SolicitudRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $repousuario;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Solicitud $model, UsuarioRepositoryInterface $repousuario)
    {
        $this->model = $model;
        $this->repousuario = $repousuario;
    }

    public function listSolicitudes()
    {
        $usuario = Auth::user();
        $usuario_area = $this->repousuario->findByEmail(
            strval($usuario->username),
            'checklist'
        );

        $solicitudes = $this->model
        ->orderBy('id', 'desc')
        ->with('solicitudColaborador', 'solicitudColaborador.checkAreaColaboradores','solicitudColaborador.SapMaestroCausalesTerminos')
        ->whereHas('solicitudColaborador.checkAreaColaboradores', function ($query) use ($usuario_area) {
            $query->where('area_id', '=', $usuario_area->id_area);
        })
        ->get();
    

        return $solicitudes;
    }
}
