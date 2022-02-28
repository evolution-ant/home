<?php

namespace App\Admin\Actions\Joke;

use App\Models\Joke;
use Encore\Admin\Actions\RowAction;

class LikeJoke extends RowAction
{
    // 在页面点击这一列的图表之后，发送请求到后端的handle方法执行
    public function handle(Joke $Joke)
    {
        $Joke->like = (int) !$Joke->like;
        $Joke->save();
        // 保存之后返回新的html到前端显示
        $html = $Joke->like ? "<i class=\"fa fa-heart\"></i>" : "<i class=\"fa fa-heart-o\"></i>";
        return $this->response()->html($html);
    }

    // 这个方法来根据`star`字段的值来在这一列显示不同的图标
    public function display($like)
    {
        return $like ? "<i class=\"fa fa-heart\"></i>" : "<i class=\"fa fa-heart-o\"></i>";
    }
}
