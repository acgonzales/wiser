<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WifiVoucher extends Model
{
    protected $fillable = [
        'code',
        'value'
    ];
}
