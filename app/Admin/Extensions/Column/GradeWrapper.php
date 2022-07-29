<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class GradeWrapper extends AbstractDisplayer
{
    protected function script($key, $value)
    {
        \Log::info(__METHOD__, ['unique_id:']);
        $value = str_replace('`', '"', $value);
        return <<<SCRIPT
        button4$key.addEventListener('click', function () {
            // 设置按钮 class = btn btn-default
            button4$key.classList.remove('btn-danger');
            button4$key.classList.add('btn-default');
            button3$key.classList.remove('btn-warning');
            button3$key.classList.add('btn-default');
            button2$key.classList.remove('btn-info');
            button2$key.classList.add('btn-default');
            button1$key.classList.remove('btn-success');
            button1$key.classList.add('btn-default');

            button4$key.classList.remove('btn-default');
            button4$key.classList.remove('btn-danger');
            button4$key.classList.add('btn-danger');
            $.get("/api/word/update", { id: $key, importance: 4 },
            function(data){
            });
        });


        button3$key.addEventListener('click', function () {
            button4$key.classList.remove('btn-danger');
            button4$key.classList.add('btn-default');
            button3$key.classList.remove('btn-warning');
            button3$key.classList.add('btn-default');
            button2$key.classList.remove('btn-info');
            button2$key.classList.add('btn-default');
            button1$key.classList.remove('btn-success');
            button1$key.classList.add('btn-default');

            button3$key.classList.remove('btn-default');
            button3$key.classList.remove('btn-warning');
            button3$key.classList.add('btn-warning');
            $.get("/api/word/update", { id: $key, importance: 3 },
            function(data){
            });
        });
        button2$key.addEventListener('click', function () {
            button4$key.classList.remove('btn-danger');
            button4$key.classList.add('btn-default');
            button3$key.classList.remove('btn-warning');
            button3$key.classList.add('btn-default');
            button2$key.classList.remove('btn-info');
            button2$key.classList.add('btn-default');
            button1$key.classList.remove('btn-success');
            button1$key.classList.add('btn-default');

            button2$key.classList.remove('btn-default');
            button2$key.classList.remove('btn-info');
            button2$key.classList.add('btn-info');
            $.get("/api/word/update", { id: $key, importance: 2 },
            function(data){
            });
        });
        button1$key.addEventListener('click', function () {
            button4$key.classList.remove('btn-danger');
            button4$key.classList.add('btn-default');
            button3$key.classList.remove('btn-warning');
            button3$key.classList.add('btn-default');
            button2$key.classList.remove('btn-info');
            button2$key.classList.add('btn-default');
            button1$key.classList.remove('btn-success');
            button1$key.classList.add('btn-default');
            
            button1$key.classList.remove('btn-default');
            button1$key.classList.remove('btn-success');
            button1$key.classList.add('btn-success');
            $.get("/api/word/update", { id: $key, importance: 1 },
            function(data){
            });
        });
        SCRIPT;
    }

    public function display($value = '')
    {
        Admin::script($this->script($this->getKey(), $value));
        $btn_style_1 = 'btn-default';
        $btn_style_2 = 'btn-default';
        $btn_style_3 = 'btn-default';
        $btn_style_4 = 'btn-default';
        if ($value == 1) {
            $btn_style_1 = 'btn-success';
        } else if ($value == 2) {
            $btn_style_2 = 'btn-info';
        } else if ($value == 3) {
            $btn_style_3 = 'btn-warning';
        } else if ($value == 4) {
            $btn_style_4 = 'btn-danger';
        }
        $text = sprintf(
            '<div class="btn-group" role="group" aria-label="...">
                    <button id = "button4%s" type="button4" class="btn %s">again</button>
                    <button id = "button3%s" type="button3" class="btn %s">hard</button>
                    <button id = "button2%s" type="button2" class="btn %s">good</button>
                    <button id = "button1%s" type="button1" class="btn %s">easy</button>
            </div>',
            $this->getKey(),
            $btn_style_4,
            $this->getKey(),
            $btn_style_3,
            $this->getKey(),
            $btn_style_2,
            $this->getKey(),
            $btn_style_1
        );
        return <<<EOT
            $text
        EOT;
    }
}
