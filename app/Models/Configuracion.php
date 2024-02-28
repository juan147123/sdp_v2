<?php

namespace App\Models;

use App\Scopes\EnableScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $parent_id
 * @property string $descripcion
 * @property string $categoria
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enable
 * @property string $pais
 * @property integer $input
 * @property Configuracione $configuracione
 * @property CheckAreaColaborador[] $checkAreaColaboradors
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
    protected $fillable = ['parent_id', 'descripcion', 'categoria', 'created_at', 'updated_at', 'enable', 'pais', 'input'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Configuracion()
    {
        return $this->hasMany('App\Models\Configuracion', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function CheckColaborador()
    {
        return $this->hasMany('App\Models\CheckColaborador', 'area_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EnableScope);
    }
}
