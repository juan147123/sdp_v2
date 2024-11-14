<?php

namespace App\Interfaces;

interface SolicitudColaboradorRepositoryInterface extends EloquentRepositoryInterface
{
    public function updateStatusMasiveAdmObr($request);
    public function updateStatusMasiveVisiObr($request);
    public function updateStatusMasiveRrhh($request);
    public function CountSolicitudByUserId($user_id);
    public function getSolicitudColaboradorPendinteAdmObr($idSolicitud,$status);
    public function getSolicitudColaboradorPendinteVisiObr($idSolicitud,$status);
    public function getSolicitudColaboradorPendinteRrhh($idSolicitud,$status);
}
