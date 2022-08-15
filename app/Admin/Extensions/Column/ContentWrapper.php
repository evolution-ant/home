<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class ContentWrapper extends AbstractDisplayer
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

    public function display($content = '')
    {
        Admin::script($this->script($this->getKey(), $content));
        // 获取当前 url 地址
        $url = url()->full();
        $word = '';
        // 用 __search__= 分割字符串
        $search = explode('__search__=', $url);
        if (count($search) > 1) {
            $search_list = $search[1];
            // 用 & 分割字符串
            $word = explode('&', $search_list)[0];
            // %E7%94%B5%E8%84%91 编码
            $word = urldecode($word);
        }

        $str = str_ireplace($word, '<font color="blue">' . $word . '</font>', $content);
        $str =  '<div class="well well-sm">' . '<button id="button' . $this->getKey() . '" title="Copied!"><i class="fa fa-clipboard"></i></button> ' . $str . '</div>';
        return <<<EOT
            $str
        EOT;
    }
}
