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
    public function todos()
    {
        return $this->hasMany(Todo::class);
    }
    public function books()
    {
        return $this->hasMany(Book::class);
    }
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }
    public function words()
    {
        return $this->hasMany(Word::class);
    }
}
