<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sentence extends Model
{
    use SoftDeletes;
    const NAME = 'sentences';
    protected $table = 'sentences';

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
