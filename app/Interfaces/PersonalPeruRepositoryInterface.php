<?php

namespace App\Interfaces;

interface PersonalPeruRepositoryInterface extends EloquentRepositoryInterface
{
    public function getDataByEmailLider($correo);
}
