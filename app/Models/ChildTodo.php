<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildTodo extends Model
{

    const NAME = 'child_todos';
    protected $table = 'child_todos';

    protected $fillable = [
        'id',
        'todo_id',
        'title',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];
    public function todo()
    {
        return $this->belongsTo(Todo::class);
    }
}
