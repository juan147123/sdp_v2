<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalChile extends Model
{
    use HasFactory;
    protected $connection = 'dw_chile';
    protected $table = "flesan_rrhh.sap_maestro_colaborador";
    public $timestamps = false;
}
