<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Code extends Model
{
    use SoftDeletes;

    const NAME = 'codes';
    protected $table = 'codes';

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
