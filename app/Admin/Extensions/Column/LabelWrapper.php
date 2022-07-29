<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class LabelWrapper extends AbstractDisplayer
{
    public function display($type_name = '', $type_id = 1)
    {
        switch ($type_id % 5) {
            case 0:
                $style = 'primary';
                break;
            case 1:
                $style = 'success';
                break;
            case 2:
                $style = 'info';
                break;
            case 3:
                $style = 'warning';
                break;
            case 4:
                $style = 'danger';
                break;
            default:
                $style = 'default';
                break;
        }
        $text = sprintf("<span class='label label-%s'>%s</span> ", $style, $this->value);
        return <<<EOT
        $text
EOT;
    }
}
