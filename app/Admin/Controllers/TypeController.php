<?php

namespace App\Admin\Controllers;

use App\Models\Type;
use App\Models\Joke;
use App\Models\Code;
use App\Models\Todo;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TypeController extends Controller
{
    use HasResourceActions;

    const GROUP_OPTIONS = [
        Joke::NAME => Joke::NAME,
        Code::NAME => Code::NAME,
        Todo::NAME => Todo::NAME,
    ];
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
    // public function show($id, Content $content)
    // {
    //     return $content
    //         ->header(trans('admin.detail'))
    //         ->description(trans('admin.description'))
    //         ->body($this->detail($id));
    // }
    public function show($id, Content $content)
    {
        $product = Type::find($id);

        return $content->title('详情')
            ->description('简介')
            ->view('product.show', $product->toArray());
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

        $grid = new Grid(new Type);
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('group', 'Group', TypeController::GROUP_OPTIONS);
        });
        
        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('name', 'name');
            $create->select('group', "group")->options($this::GROUP_OPTIONS);
        });
        $grid->id('ID');
        $grid->name('name');
        $grid->group('group');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

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
        $show = new Show(Type::findOrFail($id));

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
        $form = new Form(new Type);
        // $form->display('ID');
        $form->radio('group', "group")->required()->options($this::GROUP_OPTIONS);
        $form->text('name', 'name')->required();
        // $form->display(trans('admin.created_at'));
        // $form->display(trans('admin.updated_at'));
        return $form;
    }
}
