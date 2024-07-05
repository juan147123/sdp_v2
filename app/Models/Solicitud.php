<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $codigo
 * @property string $np_lider
 * @property string $user_created
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enable
 * @property integer $status
 * @property SolicitudColaborador[] $solicitudColaboradors
 */
class Solicitud extends Model
{

    protected $primaryKey = 'id';
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'solicitudes';

    /**
     * @var array
     */
    protected $fillable = ['codigo', 'np_lider', 'user_created', 'created_at', 'updated_at', 'enable', 'status', 'obra', 'centro_costo'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function estado()
    {
        return $this->belongsTo('App\Models\Estados', 'status', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solicitudColaborador()
    {
        return $this->hasMany('App\Models\SolicitudColaborador', 'id_solicitud');
    }

    public function solicitudColaborador2()
    {
        return $this->hasMany('App\Models\SolicitudColaborador', 'id_solicitud')->where('status','!=', 6);
    }
}
