<?php

namespace App\Http\Controllers;

use App\Models\MindMap;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class ApiArticleController extends Controller
{
    use HasResourceActions;
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content->view('admin.article');
    }
}
