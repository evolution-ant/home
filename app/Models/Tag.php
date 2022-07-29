<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $table = 'tags';

    public function joke()
    {
        return $this->belongsToMany(Joke::class);
    }
    public function code()
    {
        return $this->belongsToMany(Code::class);
    }
    public function todo()
    {
        return $this->belongsToMany(Todo::class);
    }
    public function book()
    {
        return $this->belongsToMany(Book::class);
    }
    public function collection()
    {
        return $this->belongsToMany(Collection::class);
    }
    public function word()
    {
        return $this->belongsToMany(Word::class);
    }
}
