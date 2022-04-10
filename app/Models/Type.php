<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use SoftDeletes;

    protected $table = 'types';
    public function jokes()
    {
        return $this->hasMany(Joke::class);
    }
    public function codes()
    {
        return $this->hasMany(Code::class);
    }
    public function todo()
    {
        return $this->belongsToMany(Todo::class);
    }
}
