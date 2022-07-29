<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class CodeWrapper extends AbstractDisplayer
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

    public function display($language = 'python', $title = '', $remark = '')
    {
        $value = $this->value;
        $value = str_replace('<', '&lt;', $value);
        Admin::script($this->script($this->getKey(), $value));
        $remarkHtml = '';
        if ($remark != '') {
            $remarkHtml = sprintf('<p class="text-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%s</p>', $remark);
        }
        $text = sprintf('<button id="button%s" title="Copied!"><i class="fa fa-clipboard"></i></button><b class="text-primary">         %s</b>%s
        <pre class="line-numbers"><code id="code-text-%s" class="language-%s">%s</code></pre>', $this->getKey(), $title, $remarkHtml, $this->getKey(), $language, $value);
        // $text = sprintf('<button id="button%s" title="Copied!"><i class="fa fa-clipboard"></i></button><b class="text-primary">         %s</b>
        // <pre class="line-numbers pre-scrollable"><code id="code-text-%s" class="language-%s">%s</code></pre>', $this->getKey(), $title, $this->getKey(), $language, $value);
        $text = str_replace('<?php', '', $text);
        $text = str_replace('?>', '', $text);
        return <<<EOT
        $text
EOT;
    }
}
