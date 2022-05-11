<?php

namespace App\Admin\Actions\Todo;

use App\Models\Todo;
use Encore\Admin\Actions\RowAction;

class IsDone extends RowAction
{
    // 在页面点击这一列的图表之后，发送请求到后端的handle方法执行
    public function handle(Todo $Todo)
    {
        $Todo->is_done = (int) !$Todo->is_done;
        $Todo->save();
        // 保存之后返回新的html到前端显示
        $html = $Todo->is_done == 0 ? '<button type="button" class="btn btn-default">⭕️</button>' : '<button type="button" class="btn btn-default">✅</button>';
        return $this->response()->html($html)->refresh();
    }

    // 这个方法来根据`star`字段的值来在这一列显示不同的图标
    public function display($is_done)
    {
        \Log::info(__METHOD__, ['is_done:', $is_done]);
        return $is_done == 0 ? '<button type="button" class="btn btn-default">⭕️</button>' : '<button type="button" class="btn btn-default">✅</button>';
    }
}
