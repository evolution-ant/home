<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ApiWordController extends Controller
{
    function update(Request $request)
    {
        // 获取请求的 id
        $id = $request->input('id');
        // 查询 words 表，id 为 id 的记录
        $word = \DB::table('words')->where('id', $id)->first();
        // 获取 content
        $content = $word->content;
        // 执行 shell
        $process = Process::fromShellCommandline('say "' . $content . '"');
        $process->run();
        // 获取请求的 importance
        $importance = $request->input('importance');
        // 打印请求的参数
        \Log::info(__METHOD__, ['request:', $request->all()]);
        // 更新 words 表，id 为 id 的记录，importance 为 importance 的记录
        $word = \DB::table('words')->where('id', $id)->update(['importance' => $importance]);
    }
}
