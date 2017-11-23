<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'social_id',
        'social_name',
        'social_type',
        'user_id',
        'api_token',
        'api_token_start_date',
        'status',
    ];

    protected $dates = ['api_token_start_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
