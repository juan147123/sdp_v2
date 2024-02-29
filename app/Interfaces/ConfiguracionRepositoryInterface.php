<?php

namespace App\Interfaces;

interface ConfiguracionRepositoryInterface extends EloquentRepositoryInterface
{
    public function listAll();
    public function listByIdArea();
}
