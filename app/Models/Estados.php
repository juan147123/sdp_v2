<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $user_created
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enable
 * @property string $descripcion
 */
class Estados extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_created', 'created_at', 'updated_at', 'enable', 'descripcion'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'pgsql';

}
