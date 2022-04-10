<?php

namespace App\Admin\Controllers;

use App\Models\Todo;
use App\Models\Type;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use App\Admin\Actions\Todo\Restore;


class TodoController extends Controller
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
            ->header('Todo')
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

        $grid = new Grid(new Todo);

        $grid->actions(function ($actions) {
            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
               // append一个操作
            $actions->append('<a href=""><i class="fa fa-eye"></i></a>');

            // prepend一个操作
            $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
            // $actions->append(new CheckButton($actions->getKey()));
        });

        $grid->enableHotKeys();
        $grid->quickSearch('content', 'remark');
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('type_id', 'Type', Type::where('group', Todo::NAME)->pluck('name', 'id'));
            $selector->select('tags', 'Tags',  Tag::where('group', Todo::NAME)->pluck('name', 'id'), function ($query, $value) {
                $todo_ids = [];
                foreach ($value as $id) {
                    $tmp_todos = DB::table("todo_tag")->where('tag_id', $id)->get();
                    foreach ($tmp_todos as $tmp_todo) {
                        array_push($todo_ids, $tmp_todo->todo_id);
                    }
                }
                $query->whereIn('id', $todo_ids);
            });
        });

        $grid->footer(function ($query) {
            $data = $query->where('importance', 0)->sum('importance');
            return "<div style='padding: 10px;'>总收入 ： $data</div>";
        });
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 3, function ($filter) {
                $filter->equal('type', 'Type')->select(function () {
                    $query = Type::where('group', Todo::NAME)->pluck('name', 'id');
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
            $tags = Tag::where('group', Todo::NAME)->pluck('name', 'id');
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

        $grid->column('content')->display(function () {
            $content = $this->content;
            $content = str_replace('<', '&lt;', $content);
            $content = str_replace('>', '&gt;', $content);
            $content = str_replace('"', "'", $content);

            $type_style = 'default';
            $type = Type::find($this->type_id);
            $type_name = '';
            if ($type != null) {
                \Log::info(__METHOD__, ['type_id:', $this->type_id]);
                \Log::info(__METHOD__, ['name:', $type->name]);
                $type_name = $type->name;
            }
            switch ($this->type_id % 5) {
                case 0:
                    $type_style = 'primary';
                    break;
                case 1:
                    $type_style = 'success';
                    break;
                case 2:
                    $type_style = 'info';
                    break;
                case 3:
                    $type_style = 'warning';
                    break;
                case 4:
                    $type_style = 'danger';
                    break;
                default:
                    $type_style = 'default';
                    break;
            }

            $tags = '';
            foreach ($this->tags as $tag) {
                $tag_style = '';
                switch ($tag->id % 5) {
                    case 0:
                        $tag_style = 'default';
                        break;
                    case 1:
                        $tag_style = 'primary';
                        break;
                    case 2:
                        $tag_style = 'success';
                        break;
                    case 3:
                        $tag_style = 'danger';
                        break;
                    case 4:
                        $tag_style = 'warning';
                        break;
                    case 5:
                        $tag_style = 'info';
                        break;
                    default:
                        $tag_style = 'default';
                        break;
                }
                $tags = $tags . "<span class='badge label-$tag_style'>$tag->name</span>";
            }

            $titleHtml = sprintf("<h3>%s</h3>", $this->title);
            $remarkHtml = sprintf("<h4 class='text-danger'>🧾%s</h4>", $this->remark);
            if (!$this->remark) {
                $remarkHtml = '';
            }
            \Log::info(__METHOD__, ['type_style:', $type_style]);
            \Log::info(__METHOD__, ['type_name:', $type_name]);
            $headHtml = sprintf('<div class="panel panel-%s"><div class="panel-heading">%s</div><div class="panel-footer">', $type_style, $type_name);
            $typeHtml = sprintf("<p><span class='label label-%s'>%s</span></p>", $type_style, $type_name);
            $tagsHtml = sprintf("<p>%s</p>", $tags);
            \Log::info(__METHOD__, ['tagsHtml:', $tagsHtml]);
            $contentHtml = sprintf("<pre><code>%s</code></pre>", $content);
            return sprintf('
                %s
                %s
                <p></p>
                %s
                %s
                %s
                </div>
                ', $headHtml, $titleHtml, $remarkHtml, $tagsHtml, $contentHtml);
        });

        // $grid->column('back')->modal(function ($model) {
        //     return "dsafsdfads";
        // });
        // $grid->importance('Imp')->display(function ($importance) {
        //     $html = "<i class='fa fa-star' style='color:#ff8913'></i>";
        //     if ($importance < 1) {
        //         return '';
        //     }
        //     return join('&nbsp;', array_fill(0, min(5, $importance), $html));
        // })->sortable();
        // $grid->remark('Remark')->width(300)->color('red');
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
        $show = new Show(Todo::findOrFail($id));

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
        $form = new Form(new Todo);
        $types = Type::where('group', Todo::NAME)->pluck('name', 'id');
        $form->radioCard('type_id', "type")->options($types);
        $form->text('title', 'title');
        $form->textarea('content')->required();

        $tags = Tag::where('group', Todo::NAME)->pluck('name', 'id');
        $form->listbox('tags', 'choose tags')->options($tags);
        $form->starRating('importance');
        $form->text('remark', 'remark');

        return $form;
    }
}
