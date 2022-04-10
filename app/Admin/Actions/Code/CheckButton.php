<?php

namespace App\Admin\Actions\Code;

use Encore\Admin\Admin;
use App\Models\Code;

class CheckButton
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT
        $('.check-draw-money').on('click', function () {
            $.ajax({
                type : "POST",
                url : "../api/drawMoney/check",
                dataType : "json",
                data : {
                    'draw_money_id':$(this).data('id'),
                    'type':'check'
                },
                success : function(test) {
                    window.location.reload();
                },
            });
        });

        SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        return '
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default">Left</button>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default">Middle</button>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-default">Right</button>
        </div>
        </div>';
    }

    public function __toString()
    {
        return $this->render();
    }
}
