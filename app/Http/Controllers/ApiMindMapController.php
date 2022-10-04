<?php

namespace App\Http\Controllers;

use App\Models\MindMap;
use Illuminate\Http\Request;

class ApiMindMapController extends Controller
{
    function query(Request $request)
    {
        // 获取请求的 id
        $id = $request->input('id');
        // 打印请求的参数
        \Log::info(__METHOD__, ['request:', $request->all()]);
        $mindmap = MindMap::find($id);
        if ($mindmap == NULL) {
            return response()->json(['data' => '{"nodeData": {"topic": "node topic"},"linkData": {}}']);
        }
        // 查询 words 表，id 为 id 的记录
        // 获取 content
        $js_content = $mindmap->js_content;
        // 打印 js_content
        \Log::info(__METHOD__, ['js_content:', $js_content]);
        return response()->json(['data' => $js_content]);
    }

    function create(Request $request)
    {
        // 打印请求的参数
        \Log::info(__METHOD__, ['request:', $request->all()]);
        // 获取请求的 id
        $js_content = $request->input('js_content');
        $md_content = $request->input('md_content');
        $mindmap = new MindMap();
        $mindmap->js_content = $js_content;
        $mindmap->md_content = $md_content;
        $mindmap->save();
        // 获取 id
        $id = $mindmap->id;
        return response()->json(['data' => $id]);
    }

    function update(Request $request)
    {
        // 获取请求的 id
        $id = $request->input('id');
        $js_content = $request->input('js_content');
        $md_content = $request->input('md_content');
        // 打印请求的参数
        \Log::info(__METHOD__, ['request:', $request->all()]);
        $mindmap = MindMap::find($id);
        $mindmap->js_content = $js_content;
        $mindmap->md_content = $md_content;
        $mindmap->save();
        return response()->json(['data' => '']);
    }
}
