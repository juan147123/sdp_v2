<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $motivo
 * @property integer $id_solicitud
 * @property integer $user_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $fecha_desvinculacion
 * @property string $user_created
 * @property integer $np_lider
 * @property string $nombre_completo
 * @property integer $enable
 * @property string $redireccion
 * @property string $comentario
 * @property Archivo[] $archivos
 * @property CheckAreaColaborador[] $checkAreaColaboradors
 * @property Solicitude $solicitude
 * @property SapMaestroCausalesTermino $sapMaestroCausalesTermino
 */
class SolicitudColaborador extends Model
{
    protected $primaryKey = 'id';
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'solicitud_colaborador';

    /**
     * @var array
     */
    protected $fillable = ['motivo', 'id_solicitud', 'user_id', 'status', 'created_at', 'updated_at', 'fecha_desvinculacion', 'user_created', 'np_lider', 'nombre_completo', 'enable', 'redireccion', 'comentario'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkAreaColaboradores()
    {
        return $this->hasMany('App\Models\CheckColaborador', 'id_solicitud');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function archivos()
    {
        return $this->hasMany('App\Models\Archivos', 'id_solicitud_colaborador');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solicitud()
    {
        return $this->belongsTo('App\Models\Solicitud', 'id_solicitud');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function SapMaestroCausalesTerminos()
    {
        return $this->belongsTo('App\Models\SapMaestroCausalesTerminos', 'motivo', 'externalcode');
    }
}
