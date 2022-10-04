<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;
use Symfony\Component\Process\Process;
use App\Models\Word;
use App\Models\Joke;
use App\Models\Todo;

class AlfredController extends Controller
{
    function type(Request $request)
    {
        // 获取请求的 group
        $group = $request->input('group');
        \Log::info(__METHOD__, ['group:', $group]);
        // 查询 types 表，group 为 group 的记录
        $types = Type::where('group', $group)->get();
        // 返回 types 表中 group 为 group 的记录
        // 管理者
        return response()->json([
            'types' => $types
        ]);
    }

    function todo_detail(Request $request)
    {
        // 获取请求的 title
        $title = $request->input('title');
        // 移除 title 中的 🔥⏳🎉
        $title = str_replace(['🔥', '⏳', '🎉'], '', $title);
        \Log::info(__METHOD__, ['title:', $title]);
        // 查询 types 表，group 为 group 的记录
        $todo = Todo::where('title', 'like', '%' . $title . '%')->first();
        // 获取 id
        $id = $todo->id;
        // 重定向到 todos/1/edit 路由
        return redirect('/admin/todos/' . $id . '/edit');
    }

    function todo_status(Request $request)
    {
        // 获取 title
        $title = $request->input('title');
        // 获取 status
        $status_action = $request->input('status');
        $old_status = Todo::where('title', $title)->first()->status;
        \Log::info(__METHOD__, ['title:', $title]);
        \Log::info(__METHOD__, ['status:', $status_action]);
        $message = '';
        $new_status = -1;
        switch ($status_action) {
            case 'switch':
                if ($old_status == Todo::STATUS_DONE) {
                    $new_status = Todo::STATUS_UNDO;
                    $message = '⏳[待处理]';
                }
                if ($old_status == Todo::STATUS_UNDO || $old_status == Todo::STATUS_PROGRESS) {
                    $new_status = Todo::STATUS_DONE;
                    $message = '🎉[已完成]';
                }
                break;
            case 'progress':
                $new_status = Todo::STATUS_PROGRESS;
                $message = '🔥[处理中]';
                break;
            default:
                break;
        }
        // 更新 title 的 status 字段
        Todo::where('title', $title)->update(['status' => $new_status]);
        // 返回更新后的记录
        return response()->json([
            'code' => 0,
            'message' => $message . $title
        ]);
    }

    function todo_list(Request $request)
    {
        // 获取请求的 is_undo
        $is_undo = $request->input('is_undo');
        \Log::info(__METHOD__, ['is_undo:', $is_undo]);
        if ($is_undo == 1) {
            $symbol = '=';
        } else {
            $symbol = '!=';
        }
        // 获取周日的日期
        $next_sunday = date('Y-m-d', strtotime('next Sunday'));
        // 获取上周日的日期
        $last_sunday = date('Y-m-d', strtotime('last Sunday'));
        // 查询 todos, deadline_at 小于 date 的记录
        $todos = Todo::where('deadline_at', '<=', $next_sunday)->where('deadline_at', '>=', $last_sunday)->where('status', $symbol, Todo::STATUS_DONE)->orderBy('status', 'desc')->get(['title', 'status'])->toArray();
        return response()->json([
            'todos' => $todos
        ]);
    }

    function todo_add(Request $request)
    {
        // 获取请求的 title
        $title = $request->input('title');
        // 判断 title 是否已存在
        $todo = Todo::where('title', $title)->first();
        if ($todo) {
            return response()->json([
                'code' => 1,
                'message' => '🔴[已存在]' . $title
            ]);
        }
        // 获取本周日的日期
        $date = date('Y-m-d', strtotime('next Sunday'));
        $deadline_at = $date;
        // 创建 todo 对象
        $todo = new Todo();
        // 设置 todo 对象的 title 属性为 title
        $todo->title = $title;
        $todo->deadline_at = $deadline_at;
        $todo->status = Todo::STATUS_UNDO;
        // 保存 todo 对象
        $todo->save();
        // 返回 todo 对象
        return response()->json([
            'code' => 0,
            'message' => '🟢[添加成功]' . $title
        ]);
    }

    function create(Request $request)
    {
        \Log::info(__METHOD__, ['enter']);
        // 打印请求的参数
        \Log::info(__METHOD__, ['request:', $request->all()]);
        // 获取请求的 type
        $type = $request->input('type');
        // 获取请求的 text
        $text = $request->input('text');
        // 去除前后空格
        $text = trim($text);
        // 如果 text 包含 'Excerpt From'
        if (strpos($text, '”') !== false) {
            // 用 'Excerpt From' 分割 text
            $text = explode('”', $text);
            // 获取分割后的第一个元素
            $text = $text[0];
            //拼接 text 和 '”'
            $text = $text . '”';
        }
        // 获取请求的 group
        $group = $request->input('group');
        \Log::info(__METHOD__, ['type:', $type]);
        \Log::info(__METHOD__, ['text:', $text]);
        \Log::info(__METHOD__, ['group:', $group]);
        // 查询 types 表，type 为 type 的记录
        $type = Type::where('name', $type)->where('group', $group)->first();
        $type_id = 0;
        // 如果 type 里包含 id 属性，则获取 id
        if (isset($type->id)) {
            $type_id = $type->id;
        }
        // 表名为 group
        // 判断 content 为 text 的数据是否存在
        $exist = \DB::table($group)->where('content', $text)->whereNull('deleted_at')->first();
        \Log::info(__METHOD__, ['exist:', $exist]);
        // 如果存在返回已存在
        if ($exist) {
            return response()->json([
                'code' => -1,
                'message' => '🔴[已存在] ' . $text
            ]);
        }
        $data = [
            'type_id' => $type_id,
            'content' => $text
        ];
        if ($group == 'words') {
            // translations,phonetic,explains
            \Log::info(__METHOD__, ['translations:', $request->input('translations')]);
            \Log::info(__METHOD__, ['phonetic:', $request->input('phonetic')]);
            \Log::info(__METHOD__, ['explains:', $request->input('explains')]);
            $data['translations'] = $request->input('translations');
            $data['phonetic'] = $request->input('phonetic');
            $data['explains'] = $request->input('explains');
            $data['language'] = $request->input('language');
        } else if ($group == 'collections') {
            $data['title'] = $request->input('title');
            $data['favicon'] = $request->input('favicon');
        }
        \Log::info(__METHOD__, ['data', $data]);
        // 插入 $table 表,type_id 为 type_id,content 为 text
        $id = \DB::table($group)->insertGetId($data);
        \Log::info(__METHOD__, ['插入成功 id:', $id]);
        return response()->json([
            'code' => 0,
            'message' => '🟢[添加成功] ' . $text
        ]);
    }

    function goto(Request $request)
    {
        // 获取 type
        $type_str = $request->input('type');
        // 用|分割 type，获取 group 和 name
        $type_array = explode('|', $type_str);
        $group = $type_array[0];
        $name = $type_array[1];
        \Log::info(__METHOD__, ['group:', $group]);
        \Log::info(__METHOD__, ['name:', $name]);
        // 查询 types 获取 group
        $type = Type::where('name', $name)->where('group', $group)->first();
        // type_id
        $type_id = $type->id;
        //重定向
        return redirect('/admin/' . $group . "?_selector%5Btype_id%5D=" . $type_id);
    }

    function notify_word(Request $request)
    {
        // 查询 word 表中 importance = 4 的随机一条数据
        $word = Word::where("importance", ">", 2)->inRandomOrder()->first();
        $phonetic = $word->phonetic;
        // phonetic 拼接 🎙
        $phonetic = '🎙 ' . $phonetic;
        // content 拼接
        $content = $word->content;
        $explains = $word->explains;
        // 把 <br> 替换成换行符
        $explains = str_replace('<br>', "\n", $explains);
        $cmd_notify = sprintf("osascript -e 'display notification \"%s\" with title \"%s\" subtitle \"%s\"' && say \"%s\" && say \"%s\" && say \"%s\"", $explains, $content, $phonetic, $content, $content, $content);
        $process = Process::fromShellCommandline($cmd_notify);
        $process->run();
    }

    function notify_joke(Request $request)
    {
        $joke = Joke::inRandomOrder()->first();
        $content = $joke->content;
        // 所有 emoji 表情
        $emojis = [
            '😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '☺️', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '🤑', '🤗', '🤓', '😎', '🤡', '🤠', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '😤', '😠', '😡', '😶', '😐', '😑', '😯', '😦', '😧', '😮', '😲', '😵', '😳', '😱', '😨', '😰', '😢', '😥', '🤤', '😭', '😓', '😪', '😴', '🙄', '🤔', '🤥', '😬', '🤐', '🤢', '🤧', '😷', '🤒', '🤕', '😈', '👿', '👹', '👺', '💩', '👻', '💀', '☠️', '👽', '👾', '🤖', '🎃', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾', '🙌', '👏', '👋', '👍', '👎', '👊', '✊', '✌️', '👌', '✋', '👐', '💪', '🙏', '☝️', '👆', '👇', '👈', '👉', '🖕', '🖐', '🤘'
        ];
        // 随机取一个 emoji 表情
        $emoji = $emojis[array_rand($emojis)];
        $cmd_notify = sprintf("osascript -e 'display notification \"%s\" with title \".:: %s ::.\"'", $content,  $emoji);
        $process = Process::fromShellCommandline($cmd_notify);
        $process->run();
    }
}
