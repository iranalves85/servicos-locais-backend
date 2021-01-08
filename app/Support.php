<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $table = 'support';
    protected $primaryKey = 'ID';
    protected $fillable = ['business_id', 'resource_id'];
}
