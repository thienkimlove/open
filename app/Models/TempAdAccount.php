<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempAdAccount extends Model
{
    protected $fillable = [
        'account_id',
        'social_id',
        'social_name',
        'social_type',
        'currency'
    ];
}
