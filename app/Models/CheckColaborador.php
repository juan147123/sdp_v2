<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_solicitud
 * @property boolean $aplica
 * @property integer $area_id
 * @property integer $user_id
 * @property mixed $checklist
 * @property string $comentarios
 * @property integer $enable
 * @property string $created_at
 * @property string $updated_at
 * @property string $status
 * @property SolicitudColaborador $solicitudColaborador
 */
class CheckColaborador extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'check_area_colaborador';

    /**
     * @var array
     */
    protected $fillable = ['id_solicitud', 'aplica', 'area_id', 'user_id', 'checklist', 'comentarios', 'enable', 'created_at', 'updated_at', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function solicitudColaborador()
    {
        return $this->belongsTo('App\Models\SolicitudColaborador', 'id_solicitud');
    }
}
