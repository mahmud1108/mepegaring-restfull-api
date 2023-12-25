<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $guarded = 'user_id';

    public $incrementing = false;

    public function otp()
    {
        return $this->belongsTo(UserOTP::class, 'user_id', 'user_id');
    }
}
