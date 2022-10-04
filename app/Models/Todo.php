<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use SoftDeletes;

    // 常量
    const STATUS_DONE = 0;
    const STATUS_UNDO = 1;
    const STATUS_PROGRESS = 2;
    const STATUS_UNDO_NAME = '⏳ ';
    const STATUS_DONE_NAME = '🎉 ';
    const STATUS_PROGRESS_NAME = '🔥 ';
    const NAME = 'todos';
    protected $table = 'todos';

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    public function getItemAttribute($item)
    {
        return array_values(json_decode($item, true) ?: []);
    }
    public function setItemAttribute($item)
    {
        $this->attributes['item'] = json_encode(array_values($item));
    }
}
