<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Joke extends Model
{
    use SoftDeletes;

    const NAME = 'jokes';
    protected $table = 'jokes';

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
