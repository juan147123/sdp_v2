<?php

namespace App\Interfaces;

interface AplicacionUsuarioRepositoryInterface extends EloquentRepositoryInterface
{
    public function findUserByEmail($email);
    public function listAllActive();
}
