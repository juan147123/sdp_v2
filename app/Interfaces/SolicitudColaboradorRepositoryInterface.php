<?php

namespace App\Interfaces;

interface SolicitudColaboradorRepositoryInterface extends EloquentRepositoryInterface
{
    public function updateStatusMasiveAdmObr($request);
    public function updateStatusMasiveVisiObr($status, $ids);
    public function updateStatusMasiveRrhh($status, $ids);
    public function CountSolicitudByUserId($user_id);
    public function getSolicitudColaboradorPendinteAdmObr($idSolicitud,$status);
    public function getSolicitudColaboradorPendinteVisiObr($idSolicitud,$status);
    public function getSolicitudColaboradorPendinteRrhh($idSolicitud,$status);
}
