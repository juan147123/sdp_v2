<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalPeru extends Model
{
    use HasFactory;
    protected $connection = 'dw_peru';
    protected $table = "rrhh.ejb_rrhh_planilla_ad_gf";
    public $timestamps = false;
}
