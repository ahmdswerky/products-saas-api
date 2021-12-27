<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'date',
        'base',
        'value',
    ];

    protected $casts = [
        //'date' => 'date',
    ];

    protected $dateFormat = 'Y-m-d';
    
    //public function setDateAttribute($value)
    //{
    //    $this->attributes['date'] = (new Carbon($value))->format('Y-m-d');
    //}
}
