<?php

namespace App\Interfaces;

interface ArchivoRepositoryInterface extends EloquentRepositoryInterface
{
    public function uploadFile($archivo,$id);
}
