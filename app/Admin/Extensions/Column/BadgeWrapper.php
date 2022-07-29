<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class BadgeWrapper extends AbstractDisplayer
{
    public function display($tag_names = [], $tag_ids = [])
    {
        $all_text = '';
        foreach ($tag_ids as $index => $tag_id) {
            switch ($tag_id % 5) {
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
            $text = sprintf("<span class='badge label-%s'>%s</span> ", $style, $tag_names[$index]);
            $all_text .= $text;
        }

        return <<<EOT
        $all_text
EOT;
    }
}
