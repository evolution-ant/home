<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wisesaying extends Model
{
    use SoftDeletes;

    const NAME = 'wisesayings';
    protected $table = 'wisesayings';
}
