<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function machine()
    {
        return $this->hasOne(Machine::class, 'device','device_name');
    }
}
