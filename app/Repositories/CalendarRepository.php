<?php

namespace App\Repositories;

use App\Interfaces\CalendarRepositoryInterface;
use App\Models\Calendar;
use Illuminate\Support\Facades\Storage;

class CalendarRepository extends BaseRepository implements CalendarRepositoryInterface
{
    protected $model;

    public function __construct(Calendar $model) {
        $this->model = $model;
    }


}
