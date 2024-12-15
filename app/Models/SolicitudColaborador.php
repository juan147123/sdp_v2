<?php

namespace App\Models;

use App\Scopes\EnableScope;
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
    protected $fillable = [
        'motivo',
        'id_solicitud',
        'user_id',
        'status',
        'created_at',
        'updated_at',
        'fecha_desvinculacion',
        'user_created',
        'np_lider',
        'nombre_completo',
        'enable',
        'redireccion',
        'comentario_admin_obra',
        'comentario_rrhh',
        'rut_empresa',
        'centro_costo',
        'aprobado_administrador_obra',
        'aprobado_visitador_obra',
        'aprobado_rrhh',
        'comentario_visitador',
        'full_ceco',
        'fecha_ingreso',
        'user_aprobate_admin_obra',
        'date_aprobate_admin_obra',
        'user_aprobate_visi_obra',
        'date_aprobate_visi_obra',
        'user_aprobate_rrhh_obra',
        'date_aprobate_rrhh_obra'
    ];


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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estado()
    {
        return $this->belongsTo('App\Models\Estados', 'status', 'id');
    }

    public function estadoadmin()
    {
        return $this->belongsTo('App\Models\Estados', 'aprobado_administrador_obra', 'id');
    }

    public function estadovisitador()
    {
        return $this->belongsTo('App\Models\Estados', 'aprobado_visitador_obra', 'id');
    }

    public function estadorrhh()
    {
        return $this->belongsTo('App\Models\Estados', 'aprobado_rrhh', 'id');
    }
}
