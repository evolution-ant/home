<?php

namespace App\Admin\Actions\Joke;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class QuickForm extends RowAction
{
    public $name = '快速编辑';

    public function form()
    {
        $type = [
            1 => '广告',
            2 => '违法',
            3 => '钓鱼',
        ];

        $this->checkbox('type11', '类型')->options($type);
        $this->textarea('reason', '原因')->rules('required');
    }
    public function handle(Model $model)
    {
        // $model ...

        return $this->response()->success($model->reason)->refresh();
    }
}
