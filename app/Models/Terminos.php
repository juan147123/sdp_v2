<?php

namespace App\Models;

use App\Scopes\EnableScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $externalcode
 * @property string $status
 * @property string $name
 * @property string $event
 * @property string $emplstatus
 * @property SolicitudColaborador[] $solicitudColaboradors
 */
class Terminos extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sap_maestro_causales_terminos';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'externalcode';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['status', 'name', 'event', 'emplstatus'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solicitudColaboradors()
    {
        return $this->hasMany('App\Models\SolicitudColaborador', 'motivo', 'externalcode');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EnableScope);
    }
}
