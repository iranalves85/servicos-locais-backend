<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    protected $table = 'resources';
    protected $primaryKey = 'ID';
    protected $fillable = ['name', 'request_id'];
}
