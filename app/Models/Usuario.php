<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $email
 * @property integer $id_area
 * @property string $rol
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enable
 */
class Usuario extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['email', 'id_area', 'name', 'pais', 'rol', 'created_at', 'updated_at', 'enable'];
}
