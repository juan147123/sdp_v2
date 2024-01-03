<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AplicacionUsuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $connection = 'dw_seguridad_app';
    protected $table = "seguridadapp.aplicacion_usuario";
    public $timestamps = false;
    protected $primaryKey = 'id_aplicacion_usuario';

    protected $fillable = [
        'name', 'username', 'provider', 'provider_id', 'id_aplicacion', 'fecha_ini', 'estado_sesion', 'avatar','pais'
    ];
}
