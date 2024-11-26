<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $fecha_inicio
 * @property string $fecha_fin
 * @property integer $semana
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enable
 */
class Calendar extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'calendar';

    /**
     * @var array
     */
    protected $fillable = ['fecha_inicio', 'fecha_fin', 'semana', 'created_at', 'updated_at', 'enable'];
}
