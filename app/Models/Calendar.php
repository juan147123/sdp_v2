<?php

namespace App\Models;

use App\Scopes\EnableScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $start
 * @property string $end
 * @property string $title
 * @property string $created_at
 * @property string $updated_at
 * @property integer $enable
 */
class Calendar extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'calendar';

    /**
     * @var array
     */
    protected $fillable = ['start', 'end', 'title', 'created_at', 'updated_at', 'enable'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EnableScope);
    }
}
