<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $comentario
 * @property string $id_solicitud
 * @property string $created_at
 * @property string $updated_at
 */
class Comentarios extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['comentario', 'id_solicitud', 'created_at', 'updated_at','visto','origen','usuario_name','usuario_mail'];
}
