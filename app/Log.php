<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable=['log','created_at','updated_at'];
    protected $table='logs';
}
