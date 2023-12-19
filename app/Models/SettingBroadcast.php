<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingBroadcast extends Model
{
    use HasFactory;

    protected $primaryKey = 'setting_id';

    protected $guarded = 'setting_id';

    public $incrementing = false;
}
