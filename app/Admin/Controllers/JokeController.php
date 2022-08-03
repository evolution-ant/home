<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Joke\LikeJoke;
use App\Models\Joke;
use App\Models\Type;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use App\Admin\Actions\Joke\Restore;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Admin;

class JokeController extends Controller
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
            ->header('Joke')
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

        $grid = new Grid(new Joke);
        $grid->model()->orderBy('updated_at', 'desc');

        $grid->actions(function ($actions) {
            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
            $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
            $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
        });

        $grid->enableHotKeys();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/types?&_selector%5Bgroup%5D=jokes" class="btn btn-success btn-sm" role="button">Type</a>');
            $tools->append('<a href="/admin/tags?&_selector%5Bgroup%5D=jokes" class="btn btn-danger btn-sm" role="button">Tag</a>');
        });
        $grid->quickSearch('content', 'remark');

        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('type_id', 'Type', Type::where('group', Joke::NAME)->pluck('name', 'id'));
            $selector->select('tags', 'Tags',  Tag::where('group', Joke::NAME)->pluck('name', 'id'), function ($query, $value) {
                $joke_ids = [];
                foreach ($value as $id) {
                    $tmp_jokes = DB::table("joke_tag")->where('tag_id', $id)->get();
                    foreach ($tmp_jokes as $tmp_joke) {
                        array_push($joke_ids, $tmp_joke->joke_id);
                    }
                }
                $query->whereIn('id', $joke_ids);
            });
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 3, function ($filter) {
                $filter->equal('type', 'Type')->select(function () {
                    $query = Type::where('group', Joke::NAME)->pluck('name', 'id');
                    return $query;
                });
            });
            $filter->column(1 / 3, function ($filter) {
                $filter->like('content');
            });
            $filter->column(1 / 3, function ($filter) {
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereHas('tags', function ($query) use ($input) {
                        $query->where('name', $input);
                    });
                }, 'Has tag', 'tag');
            });
        });

        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('content', 'Content');
            $types = Type::all()->pluck('name', 'id');
            $create->select('type_id', "Type")->options($types);
            $tags = Tag::where('group', Joke::NAME)->pluck('name', 'id');
            $create->multipleSelect('tags', 'Tags')->options($tags);
            $create->select('importance', 'Importance')->options([
                1 => '⭐️',
                2 => '⭐️ ⭐️',
                3 => '⭐️ ⭐️ ⭐️',
                4 => '⭐️ ⭐️ ⭐️ ⭐️',
                5 => '⭐️ ⭐️ ⭐️ ⭐️ ⭐️'
            ])->default(1);
            $create->text('remark', 'Remark');
        });
        $grid->column('type.name')->display(function ($gg, $column) {
            return $column->labelWrapper($this->type->name, $this->type_id);
        });
        $grid->content()->codeWrapper('go');
        $grid->column('like')->action(LikeJoke::class);
        $grid->column('created_at')->hide();
        $grid->column('updated_at')->hide();
        $grid->column('id')->hide();
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
        $show = new Show(Joke::findOrFail($id));

        $show->id('ID');
        $show->content('content');
        $show->type_id('type_id');
        $show->tags('标签')->as(function ($tags) {
            return $tags->pluck('name');
        })->badge();
        $show->remark('remark');
        $show->reading_times('reading_times');
        $show->importance('importance');
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
        $form = new Form(new Joke);
        $types = Type::where('group', Joke::NAME)->pluck('name', 'id');
        $form->radioCard('type_id', "type")->options($types);
        $form->textarea('content')->required();

        $tags = Tag::where('group', Joke::NAME)->pluck('name', 'id');
        $form->listbox('tags', 'choose tags')->options($tags);
        $form->text('remark', 'remark');

        return $form;
    }
}
