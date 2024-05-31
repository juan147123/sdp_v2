<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioRol extends Model
{
    use HasFactory;
    protected $connection = 'dw_seguridad_app';
    protected $table = "seguridadapp.usuario_rol";
    public $timestamps = false;
    protected $primaryKey = 'id_usuario_rol';
    protected $fillable = [
        'id_aplicacion_usuario', 'id_rol', 'fecha_ini','objeto_permitido'
    ];
}
