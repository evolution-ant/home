<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;

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
        return response()->json([
            'types' => $types
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
        $type = Type::where('name', $type)->first();
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
        $type = Type::where('name', $name)->first();
        // type_id
        $type_id = $type->id;
        //重定向
        return redirect('/admin/' . $group . "?_selector%5Btype_id%5D=" . $type_id);
    }
}
