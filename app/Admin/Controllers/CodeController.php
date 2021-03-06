<?php

namespace App\Admin\Controllers;

use App\Models\Code;
use App\Models\Type;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use App\Admin\Actions\Code\Restore;
use App\Admin\Actions\Code\LikeCode;

class CodeController extends Controller
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
            ->header('Code')
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

        $grid = new Grid(new Code);
        $grid->model()->orderBy('updated_at', 'desc');

        $grid->actions(function ($actions) {
            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
            $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
            $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/types?&_selector%5Bgroup%5D=codes" class="btn btn-success btn-sm" role="button">Type</a>');
            $tools->append('<a href="/admin/tags?&_selector%5Bgroup%5D=codes" class="btn btn-danger btn-sm" role="button">Tag</a>');
            $tools->append('<a href="/admin/code" class="btn btn-warning btn-sm" role="button">Clear</a>');
        });
        $grid->enableHotKeys();
        $grid->quickSearch(function ($model, $query) {
            $model->where('title', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%")->orWhere('remark', 'like', "%{$query}%");
        });
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('type_id', 'Type', Type::where('group', Code::NAME)->pluck('name', 'id'));
            $selector->select('tags', 'Tags',  Tag::where('group', Code::NAME)->pluck('name', 'id'), function ($query, $value) {
                $code_ids = [];
                foreach ($value as $id) {
                    $tmp_codes = DB::table("code_tag")->where('tag_id', $id)->get();
                    foreach ($tmp_codes as $tmp_code) {
                        array_push($code_ids, $tmp_code->code_id);
                    }
                }
                $query->whereIn('id', $code_ids);
            });
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 3, function ($filter) {
                $filter->equal('type', 'Type')->select(function () {
                    $query = Type::where('group', Code::NAME)->pluck('name', 'id');
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
            $tags = Tag::where('group', Code::NAME)->pluck('name', 'id');
            $create->multipleSelect('tags', 'Tags')->options($tags);
            $create->text('remark', 'Remark');
        });
        $grid->column('like')->action(LikeCode::class);
        $grid->column('type.name', 'PL')->display(function ($name, $column) {
            $name = '';
            $id = 0;
            if ($this->type != null) {
                $name = $this->type->name;
                $id = $this->type->id;
            }
            return $column->languageWrapper($name);
        });
        $grid->column('content')->display(function ($content, $column) {
            $name = '';
            if ($this->type != null) {
                $name = $this->type->name;
            }
            return $column->codeWrapper($name, $this->title, $this->remark);
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
            <a href="/admin/code/%s/edit" class="btn btn-success"><i class="glyphicon glyphicon-edit"></i></a>
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
        $show = new Show(Code::findOrFail($id));

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
        $form = new Form(new Code);
        $types = Type::where('group', Code::NAME)->pluck('name', 'id');
        $form->radioCard('type_id', "type")->options($types);
        $form->text('title', 'title');
        $form->textarea('content')->required();

        $tags = Tag::where('group', Code::NAME)->pluck('name', 'id');
        $form->listbox('tags', 'choose tags')->options($tags);
        $form->text('remark', 'remark');
        $form->saving(function (Form $form) {
            // dd($form);
            \Log::info(__METHOD__, ['$text :', $form->text]);
        });
        // $form->confirm('确定更新吗？', 'edit');

        // $form->confirm('确定创建吗？', 'create');

        // $form->confirm('确定提交吗？');
        return $form;
    }
}
