<?php

namespace App\Admin\Controllers;

use App\Models\Book;
use App\Models\Type;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use App\Admin\Actions\Book\Restore;
use App\Admin\Actions\Book\LikeBook;

class BookController extends Controller
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
            ->header('Book')
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

        $grid = new Grid(new Book);
        $grid->model()->orderBy('updated_at', 'desc');

        $grid->actions(function ($actions) {
            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
            $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
            $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/types?&_selector%5Bgroup%5D=books" class="btn btn-success btn-sm" role="button">Type</a>');
            $tools->append('<a href="/admin/tags?&_selector%5Bgroup%5D=books" class="btn btn-danger btn-sm" role="button">Tag</a>');
            $tools->append('<a href="/admin/books" class="btn btn-warning btn-sm" role="button">Clear</a>');
        });
        $grid->enableHotKeys();
        $grid->quickSearch(function ($model, $query) {
            $model->where('content', 'like', "%{$query}%");
        });
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('type_id', 'Type', Type::where('group', Book::NAME)->pluck('name', 'id'));
            $selector->select('tags', 'Tags',  Tag::where('group', Book::NAME)->pluck('name', 'id'), function ($query, $value) {
                $book_ids = [];
                foreach ($value as $id) {
                    $tmp_codes = DB::table("book_tag")->where('tag_id', $id)->get();
                    foreach ($tmp_codes as $tmp_code) {
                        array_push($book_ids, $tmp_code->book_id);
                    }
                }
                $query->whereIn('id', $book_ids);
            });
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 3, function ($filter) {
                $filter->equal('type', 'Type')->select(function () {
                    $query = Type::where('group', Book::NAME)->pluck('name', 'id');
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
            $tags = Tag::where('group', Book::NAME)->pluck('name', 'id');
            $create->multipleSelect('tags', 'Tags')->options($tags);
            $create->text('remark', 'Remark');
        });
        $grid->column('like')->action(LikeBook::class);
        $grid->column('type.name')->display(function ($gg, $column) {
            return $column->labelWrapper($this->type->name, $this->type_id);
        });
        $grid->column('content')->display(function ($content, $column) {
            return $column->contentWrapper($this->content);
        });
        $grid->column('tags')->display(function ($tags, $column) {
            $tag_names = [];
            $tag_ids = [];
            foreach ($tags as $tag) {
                array_push($tag_names, $tag['name']);
                array_push($tag_ids, $tag['id']);
            }
            if (count($tag_names) == 0) {
                return '';
            }
            return $column->badgeWrapper($tag_names, $tag_ids);
        });

        $grid->column('Edit')->display(function () {
            return sprintf('<div class="list-group">
            <a href="/admin/book/%s/edit" class="btn btn-success"><i class="glyphicon glyphicon-edit"></i></a>
            </div>', $this->id);
        });


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
        $show = new Show(Book::findOrFail($id));

        $show->id('ID');
        $show->content('content');
        $show->type_id('type_id');
        $show->tags('标签')->as(function ($tags) {
            return $tags->pluck('name');
        })->badge();
        $show->remark('remark');
        $show->reading_times('reading_times');
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
        $form = new Form(new Book);
        $types = Type::where('group', Book::NAME)->pluck('name', 'id');
        $form->radioCard('type_id', "type")->options($types);
        $form->text('title', 'title');
        $form->textarea('content')->required();

        $tags = Tag::where('group', Book::NAME)->pluck('name', 'id');
        $form->listbox('tags', 'choose tags')->options($tags);
        $form->text('remark', 'remark');
        $form->saving(function (Form $form) {
            \Log::info(__METHOD__, ['$text :', $form->text]);
        });
        return $form;
    }
}
