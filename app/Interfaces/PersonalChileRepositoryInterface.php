<?php

namespace App\Interfaces;

interface PersonalChileRepositoryInterface extends EloquentRepositoryInterface
{
    public function getNpLideres();
    public function UserFindByEmail($email);
    public function getNpLiderByEmail($email);
    public function getDataByUserIdLider($userId);
    public function getLideresObraCl();
    public function getColaboradoresObra($correo);
    public function getAprobadorObraCL($correo);
    public function getAdministradorDepartamento();
}
