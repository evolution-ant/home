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
use App\Admin\Actions\Todo\IsDone;

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
        });

        $grid->enableHotKeys();
        $grid->quickSearch('content', 'remark');
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/types?&_selector%5Bgroup%5D=todos" class="btn btn-success btn-sm" role="button">Type</a>');
            $tools->append('<a href="/admin/tags?&_selector%5Bgroup%5D=todos" class="btn btn-danger btn-sm" role="button">Tag</a>');
        });
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
            $selector->selectOne('importance', 'Imp', [
                3 => '⭐️⭐️⭐️ 高',
                2 => '⭐️⭐️ 中',
                1 => '⭐️ 低',
            ]);
            $selector->selectOne('is_done', 'IsDone', [
                0 => '⭕️ 未完成',
                1 => '✅ 已完成',
            ]);
            $selector->select('deadline_at', 'deadline_at',  ['today' => 'today', 'week' => 'week', 'month' => 'month'], function ($query, $value) {
                \Log::info(__METHOD__, ['date:', date("Y-m")]);
                switch ($value) {
                    case 'today':
                        $query->where('deadline_at', date("Y-m-d"));
                        break;
                        // case 'week':
                        //     $query->where('deadline_at', date("Y-m-d"));
                        //     break;
                    case 'month':
                        $query->like('deadline_at', date("Y-m"));
                        break;
                    default:
                        $query->where('deadline_at', date("Y-m-d"));
                        break;
                }
            });
        });

        $grid->footer(function ($query) {
            $data = $query->where('importance', 0)->sum('importance');
            return "<div style='padding: 10px;'>总收入 ： $data</div>";
        });
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 2, function ($filter) {
                $filter->equal('type', 'Type')->select(function () {
                    $query = Type::where('group', Todo::NAME)->pluck('name', 'id');
                    return $query;
                });
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('content');
            });

            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereHas('tags', function ($query) use ($input) {
                        $query->where('name', $input);
                    });
                }, 'Has tag', 'tag');
            });
        });

        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('title', 'Title');
            $create->text('content', 'Content');
            $types = Type::where('group', Todo::NAME)->pluck('name', 'id');
            $create->select('type_id', "Type")->options($types);
            $tags = Tag::where('group', Todo::NAME)->pluck('name', 'id');
            $create->multipleSelect('tags', 'Tags')->options($tags);
            $create->select('importance', 'Importance')->options([
                1 => '⭐️',
                2 => '⭐️ ⭐️',
                3 => '⭐️ ⭐️ ⭐️',
            ])->default(1);
            // $create->text('remark', 'Remark');
        });

        $grid->column('id');
        $grid->column('is_done')->action(IsDone::class);
        $grid->column('title')->display(function ($title) {
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
            switch ($this->importance) {
                case 1:
                    $title_style = 'success';
                    break;
                case 2:
                    $title_style = 'warning';
                    break;
                case 3:
                    $title_style = 'danger';
                    break;
                default:
                    $title_style = 'danger';
                    break;
            }
            if ($this->is_done == 0) {
                return sprintf('<p class="text-%s">%s %s</p>', $title_style, $tags, $title);
            } else {
                return sprintf('<p class="text-%s"><del>%s %s</del></p>', $title_style, $tags, $title);
            }
        });
        $grid->column('importance')->display(function ($importance) {
            switch ($importance) {
                case 1:
                    $star_str = '⭐️';
                    break;
                case 2:
                    $star_str = '⭐️⭐️';
                    break;
                case 3:
                    $star_str = '⭐️⭐️⭐️';
                    break;
                default:
                    $star_str = '⭐️⭐️⭐️';
                    break;
            }
            return $star_str;
        })->sortable();
        $grid->column('deadline_at')->display(function ($deadline_at) {
            return str_replace(' 00:00:00', '', $deadline_at);
        })->sortable();
        $grid->column('content')->width(200);
        $grid->column('created_at')->hide();
        $grid->column('updated_at')->hide();
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
        $form->text('title', 'title')->required();
        $form->date('deadline_at', 'deadline_at')->format('YYYY-MM-DD');
        $tags = Tag::where('group', Todo::NAME)->pluck('name', 'id');
        $form->listbox('tags', 'choose tags')->options($tags);
        // $form->starRating('importance');
        $form->textarea('content');
        $form->text('remark', 'remark');

        return $form;
    }
}
