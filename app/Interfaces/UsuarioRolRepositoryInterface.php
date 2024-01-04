<?php

namespace App\Interfaces;

interface UsuarioRolRepositoryInterface extends EloquentRepositoryInterface
{
    public function getIdRol();
}
