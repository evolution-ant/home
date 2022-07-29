<?php

namespace App\Admin\Controllers;

use App\Models\Tag;
use App\Admin\Controllers\TypeController;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TagController extends Controller
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
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
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
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
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
        $grid = new Grid(new Tag);
        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('name', 'name');
            $create->select('group', "group")->options(TypeController::GROUP_OPTIONS);
        });
        $grid->id('ID');
        $grid->name('name');
        $grid->group('group');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        // 获取当前 url
        $full_url = url()->full();
        $group = '';
        // 如果 url 中含有 group 参数，则设置 group 参数为当前 group 参数
        if (strpos($full_url, 'group') !== false) {
            // 获取 group 参数
            $group = explode('?', $full_url)[1];
            $group = explode('%5D=', $group)[1];
        }

        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) use ($group) {
            $create->text('name', 'name');
            $create->select('group', "group")->options(TypeController::GROUP_OPTIONS)->default($group);
        });

        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('group', 'Group', TypeController::GROUP_OPTIONS);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Tag::findOrFail($id));

        $show->id('ID');
        $show->name('name');
        $show->group('group');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Tag);
        $form->radio('group', "group")->options(TypeController::GROUP_OPTIONS);
        $form->text('name', 'name');
        return $form;
    }
}
