<?php

namespace App\Admin\Actions\Book;

use App\Models\Book;
use Encore\Admin\Actions\RowAction;

class LikeBook extends RowAction
{
    // 在页面点击这一列的图表之后，发送请求到后端的handle方法执行
    public function handle(Book $Book)
    {
        $Book->like = (int) !$Book->like;
        $Book->save();
        // 保存之后返回新的html到前端显示
        $html = $Book->like == 1 ? '<button type="button" class="btn btn-default">🌝</button>' : '<button type="button" class="btn btn-default">🌚</button>';
        return $this->response()->html($html);
    }

    // 这个方法来根据`star`字段的值来在这一列显示不同的图标
    public function display($like)
    {
        \Log::info(__METHOD__, ['like:', $like]);
        return $like == 1 ? '<button type="button" class="btn btn-default">🌝</button>' : '<button type="button" class="btn btn-default">🌚</button>';
    }
}
