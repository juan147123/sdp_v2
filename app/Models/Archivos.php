<?php

namespace App\Models;

use App\Scopes\EnableScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_solicitud_colaborador
 * @property string $name
 * @property string $extension
 * @property string $path
 * @property string $created_at
 * @property string $updated_at
 * @property string $uuid_name
 * @property integer $enable
 * @property SolicitudColaborador $solicitudColaborador
 */
class Archivos extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'id_solicitud_colaborador',
        'name',
        'extension',
        'path',
        'created_at',
        'updated_at',
        'uuid_name',
        'enable',
        'carpeta',
        'uuid',
        'origen',
        'titulo',
        'id_solicitud'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solicitudColaborador()
    {
        return $this->belongsTo('App\Models\SolicitudColaborador', 'id_solicitud_colaborador');
    }
    
    public function solicitud()
    {
        return $this->belongsTo('App\Models\Solicitud', 'id_solicitud');
    }
    
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EnableScope);
    }
}
