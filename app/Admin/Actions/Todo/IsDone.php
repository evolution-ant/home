<?php

namespace App\Admin\Actions\Todo;

use App\Models\Todo;
use Encore\Admin\Actions\RowAction;

class IsDone extends RowAction
{
    // 在页面点击这一列的图表之后，发送请求到后端的handle方法执行
    public function handle(Todo $Todo)
    {
        if ($Todo->status == Todo::STATUS_UNDO || $Todo->status == Todo::STATUS_PROGRESS) {
            $Todo->status = Todo::STATUS_DONE;
        } else {
            $Todo->status = Todo::STATUS_UNDO;
        }
        $Todo->save();
        $icon = '';
        switch ($Todo->status) {
            case Todo::STATUS_DONE:
                $icon = Todo::STATUS_DONE_NAME;
                break;
            case Todo::STATUS_UNDO:
                $icon = Todo::STATUS_UNDO_NAME;
                break;
            case Todo::STATUS_PROGRESS:
                $icon = Todo::STATUS_PROGRESS_NAME;
                break;
        }
        $html = '<button type="button" class="btn btn-default">' . $icon . '</button>';
        return $this->response()->html($html)->refresh();
    }

    // 这个方法来根据`star`字段的值来在这一列显示不同的图标
    public function display($status)
    {
        $icon = '';
        switch ($status) {
            case Todo::STATUS_DONE:
                $icon = Todo::STATUS_DONE_NAME;
                break;
            case Todo::STATUS_UNDO:
                $icon = Todo::STATUS_UNDO_NAME;
                break;
            case Todo::STATUS_PROGRESS:
                $icon = Todo::STATUS_PROGRESS_NAME;
                break;
        }
        $html = '<button type="button" class="btn btn-default">' . $icon . '</button>';
        return $html;
    }
}
