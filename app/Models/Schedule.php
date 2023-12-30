<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';

    protected $guarded = 'schedule_id';

    public $incrementing = false;

    public function package()
    {
        return  $this->belongsTo(Package::class, 'package_id', 'package_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function schedule_detail()
    {
        return $this->hasMany(ScheduleDetail::class, 'schedule_id', 'schedule_id');
    }
}
