<?php

namespace App\Admin\Actions\Joke;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Restore extends RowAction
{
    public $name = 'restore';

    public function handle(Model $model)
    {
        $model->restore();

        return $this->response()->success('restored')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定恢复吗？');
    }
}
