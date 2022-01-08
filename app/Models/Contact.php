<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'subject',
        'first_name',
        'last_name',
        'email',
        'message',
    ];
}
