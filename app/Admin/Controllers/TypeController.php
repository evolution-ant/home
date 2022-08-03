<?php

namespace App\Admin\Controllers;

use App\Models\Type;
use App\Models\Joke;
use App\Models\Collection;
use App\Models\Book;
use App\Models\Code;
use App\Models\Todo;
use App\Http\Controllers\Controller;
use App\Models\Word;
use App\Models\Sentence;
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
        Book::NAME => Book::NAME,
        Collection::NAME => Collection::NAME,
        Word::NAME => Word::NAME,
        Sentence::NAME => Sentence::NAME,
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
        $grid->model()->orderBy('group');
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('group', 'Group', TypeController::GROUP_OPTIONS);
        });
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
            $create->select('group', "group")->options($this::GROUP_OPTIONS)->default($group);
        });
        $grid->id('ID');
        $grid->name('name');
        $grid->column('group')->labelWrapper();
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
