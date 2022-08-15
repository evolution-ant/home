<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class SentenceWrapper extends AbstractDisplayer
{
    protected function script($key, $en_sentence)
    {
        \Log::info(__METHOD__, ['unique_id:']);
        $en_sentence = str_replace('`', '"', $en_sentence);
        return <<<SCRIPT
        btn$key.addEventListener('click', function () {
            $.get("/api/words/sound", { content: `$en_sentence` },
            function(data){
            });
        });
        SCRIPT;
    }

    public function display($content = '', $en_sentence = '', $zh_sentence = '')
    {
        if (empty($zh_sentence)) {
            $zh_sentence = $zh_sentence;
        } else {
            $zh_sentence = '🎙 ' . $zh_sentence;
        }
        Admin::script($this->script($this->getKey(), $en_sentence));
        $sentence = sprintf('<div>%s<br></div><div id="btn%s">%s</div> ',  $en_sentence, $this->getKey(), $zh_sentence);
        $sentence = str_ireplace($content, '<font size="3" color="blue">' . $content . '</font>', $sentence);
        return <<<EOT
            $sentence
        EOT;
    }
}
