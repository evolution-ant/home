<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class ChildTodoWrapper extends AbstractDisplayer
{
    protected function script($key, $value)
    {
        $value = str_replace('`', '"', $value);
        return <<<SCRIPT
        button$key.addEventListener('click', function () {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(`$value`);
            }
        });
        SCRIPT;
    }

    public function display($items = [])
    {
        // Admin::script($this->script($this->getKey(), $items));
        // $str = str_ireplace($word, '<font color="blue">' . $word . '</font>', $content);
        $str =  '<div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Panel heading</div>
        <div class="panel-body">
            <p>...</p>
        </div>
        <!-- Table -->
        <table class="table">
            ...
        </table>
       </div>';
        return <<<EOT
            $str
        EOT;
    }
}
