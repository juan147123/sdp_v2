<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolAplicacion extends Model
{
    use HasFactory;
    protected $connection = 'dw_seguridad_app';
    protected $table = "seguridadapp.rol_aplicacion";
    public $timestamps = false;
    protected $primaryKey = 'id_rol';
}
