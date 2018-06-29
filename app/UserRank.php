<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRank extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'machine_id', 'user_id', 'user_nickname', 'rank', 'machine_rank',
    ];

    protected $casts = [
        'machine_id' => 'integer',
        'user_id' => 'integer',
        'rank' => 'integer',
        'machine_rank' => 'integer'
        ];
}