<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FbAccount extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'fb_token',
        'fb_token_start',
        'is_filled_old_data',
    ];

    protected $dates = ['fb_token_start'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adAccounts()
    {
        return $this->hasMany(AdAccount::class, 'fb_account_id', 'id');
    }
}
