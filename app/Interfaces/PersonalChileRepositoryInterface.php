<?php

namespace App\Interfaces;

interface PersonalChileRepositoryInterface extends EloquentRepositoryInterface
{
    public function getNpLideres();
    public function UserFindByEmail($email);
    public function getNpLiderByEmail($email);
    public function getDataByUserIdLider($userId);
    public function getLideresObraCl();
    public function getLiderObraCl($correo);
    public function getColaboradoresObra($correo);
    public function getAdministradorDepartamento($correo);
    public function getVisitadorDepartamento($correo);
    public function getAprobadorObraCL($correo);
    public function getVisitadorObraCL($correo);
}
