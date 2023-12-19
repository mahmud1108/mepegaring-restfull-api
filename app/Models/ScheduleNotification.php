<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleNotification extends Model
{
    use HasFactory;
    protected $primaryKey = 'schedule_notification_id';

    protected $guarded = 'schedule_notification_id';

    public $incrementing = false;
}
