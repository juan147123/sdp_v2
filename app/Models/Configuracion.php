<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $descripcion
 * @property string $categoria
 * @property integer $parent_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enable
 * @property string $pais
 */
class Configuracion extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'configuraciones';

    /**
     * @var array
     */
    protected $fillable = ['descripcion', 'categoria', 'parent_id', 'created_at', 'updated_at', 'enable', 'pais'];
}
