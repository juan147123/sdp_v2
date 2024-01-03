<?php

namespace App\Interfaces;

interface PersonalPeruRepositoryInterface extends EloquentRepositoryInterface
{
    public function getDataByEmailLider($correo);
    public function getDniLideres();
    public function UserFindByEmail($email);
}
