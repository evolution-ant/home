<?php

namespace App\Admin\Controllers;

use App\Models\MindMap;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class MindMapController extends Controller
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
        return $content
            ->header('MindMap')
            ->description(' ')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        // return $content
        //     ->header(trans('admin.detail'))
        //     ->description(trans('admin.description'))
        //     ->body($this->detail($id));
        return $content->title('详情')
            ->description('简介')
            ->view('product.show', array(
                'content' => 'content'
            ));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->body($this->form());
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new MindMap);
        $grid->model()->orderBy('updated_at', 'desc');

        $grid->actions(function ($actions) {
            $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
            $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
        });
        $grid->disableCreateButton();
        $grid->enableHotKeys();
        $grid->quickSearch('md_content');
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/mindmaps" class="btn btn-warning btn-sm" role="button">Clear</a>');
            $tools->append('<a href="/admin/mindmap?id=0" class="btn btn-success btn-sm" role="button">New</a>');
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 3, function ($filter) {
                $filter->like('content');
            });
        });

        $grid->column('md_content')->display(function ($md_content, $column) {
            return $column->contentWrapper($md_content);
        });
        $grid->column('Edit')->display(function () {
            return sprintf('<div class="list-group">
            <a href="/admin/mindmap?id=%s" class="btn btn-success"><i class="glyphicon glyphicon-edit"></i></a>
            </div>', $this->id);
        });
        $grid->column('created_at')->hide();
        $grid->column('updated_at')->hide();
        $grid->column('id')->hide();
        return $grid;
    }
}
