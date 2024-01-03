<?php

namespace App\Interfaces;

interface UsuarioRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByEmail($email);
}
