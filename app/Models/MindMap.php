<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MindMap extends Model
{
    use SoftDeletes;

    const NAME = 'mindmaps';
    protected $table = 'mindmaps';
}
