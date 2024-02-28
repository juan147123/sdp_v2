<?php

namespace App\Interfaces;

interface SolicitudRepositoryInterface extends EloquentRepositoryInterface
{
    public function listSolicitudes();
}
