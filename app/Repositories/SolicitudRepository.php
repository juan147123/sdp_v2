<?php

namespace App\Repositories;

use App\Models\{Solicitud};
use App\Interfaces\{CheckColaboradorRepositoryInterface, SolicitudRepositoryInterface, UsuarioRepositoryInterface};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudRepository extends BaseRepository implements SolicitudRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $repousuario;
    protected $repoCheckColaborador;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Solicitud $model, UsuarioRepositoryInterface $repousuario, CheckColaboradorRepositoryInterface $repoCheckColaborador)
    {
        $this->model = $model;
        $this->repousuario = $repousuario;
        $this->repoCheckColaborador = $repoCheckColaborador;
    }

    public function listSolicitudes()
    {
        $usuario = Auth::user();
        $usuario_area = $this->repousuario->findByEmail(
            strval($usuario->username),
            env("ADMINISTRADOR_AREA")
        );

        $solicitudes = $this->model
            ->orderBy('id', 'desc')
            ->with(['solicitudColaborador', 'solicitudColaborador.SapMaestroCausalesTerminos'])
            ->get()
            ->map(function ($solicitud) use ($usuario_area) {

                // Verifica que 'solicitudColaborador' exista
                $colaboradores = $solicitud->solicitudColaborador;

                if ($colaboradores) {
                    foreach ($colaboradores as $colaborador) {
                        // Asigna el resultado del filtro al colaborador
                        $checks = $this->repoCheckColaborador
                        ->all()
                        ->where('user_id', $colaborador->user_id)
                        ->where('area_id', $usuario_area->id_area);
                        $colaborador->checkList = $checks;
                    }
                }

                return $solicitud;
            });

        return $solicitudes;
    }
}
