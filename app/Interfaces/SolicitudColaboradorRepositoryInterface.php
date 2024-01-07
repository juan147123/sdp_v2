<?php

namespace App\Interfaces;

interface SolicitudColaboradorRepositoryInterface extends EloquentRepositoryInterface
{
    public function updateStatusMasive($status, $ids);
    public function getSolicitudColaboradorPendinte($idSolicitud);
    public function CountSolicitudByUserId($user_id);
}
