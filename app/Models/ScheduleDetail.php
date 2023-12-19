<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_details_id';

    protected $guarded = 'schedule_details_id';

    public $incrementing = false;
}
