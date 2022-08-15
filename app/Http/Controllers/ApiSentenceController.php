<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ApiSentenceController extends Controller
{
    function update(Request $request)
    {
        // 获取请求的 id
        $id = $request->input('id');
        // 查询 sentences 表，id 为 id 的记录
        $word = \DB::table('sentences')->where('id', $id)->first();
        // 获取 content
        $translations = $word->translations;
        // 执行 shell
        $process = Process::fromShellCommandline('say "' . $translations . '"');
        $process->run();
        // 获取请求的 importance
        $importance = $request->input('importance');
        // 打印请求的参数
        \Log::info(__METHOD__, ['request:', $request->all()]);
        // 更新 words 表，id 为 id 的记录，importance 为 importance 的记录
        $word = \DB::table('words')->where('id', $id)->update(['importance' => $importance]);
    }
}
