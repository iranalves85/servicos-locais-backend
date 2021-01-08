<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unity extends Model
{
    protected $table = 'unity';
    protected $primaryKey = 'ID';
    protected $fillable = ['user_id', 'name', 'address', 'complement', 'number', 'neighborhood', 'city', 'state', 'zipcode'];
}
