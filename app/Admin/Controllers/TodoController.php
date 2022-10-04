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
use Encore\Admin\Widgets\Table;


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
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
        // return $content->title('详情')
        //     ->description('简介')
        //     ->view('product.show', array(
        //         'content' => 'content'
        //     ));
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
        $grid->model()->orderBy('status', 'desc');
        $grid->actions(function ($actions) {
            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
        });

        $grid->enableHotKeys();
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/types?&_selector%5Bgroup%5D=todos" class="btn btn-success btn-sm" role="button">Type</a>');
            $tools->append('<a href="/admin/tags?&_selector%5Bgroup%5D=todos" class="btn btn-danger btn-sm" role="button">Tag</a>');
            $tools->append('<a href="/admin/todos" class="btn btn-warning btn-sm" role="button">Clear</a>');
        });
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
            $selector->selectOne('importance', 'Imp', [
                3 => '⭐️⭐️⭐️',
                2 => '⭐️⭐️',
                1 => '⭐️',
            ]);
            $selector->selectOne(
                'status',
                [
                    Todo::STATUS_PROGRESS => Todo::STATUS_PROGRESS_NAME . 'Progress',
                    Todo::STATUS_UNDO => Todo::STATUS_UNDO_NAME . 'Undo',
                    Todo::STATUS_DONE => Todo::STATUS_DONE_NAME . 'Done',
                ]
            );
            // 获取本周最后一天
            $selector->selectOne('deadline_at', 'deadline_at',  ['today' => 'today', 'week' => 'week', 'month' => 'month'], function ($query, $value) {
                switch ($value) {
                    case 'today':
                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        // 打印 $tomorrow
                        \Log::info(__METHOD__, ['tomorrow', $tomorrow]);
                        $query->where('deadline_at', '<', $tomorrow);
                        break;
                    case 'week':
                        $sunday = date('Y-m-d', strtotime('+1 sunday'));
                        \Log::info(__METHOD__, ['week', $sunday]);
                        $query->where('deadline_at', '<', $sunday);
                        break;
                    case 'month':
                        $lastday = date('Y-m-d', strtotime('last day of this month'));
                        \Log::info(__METHOD__, ['month', $lastday]);
                        $query->where('deadline_at', '<', $lastday);
                        break;
                }
            });
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
            ])->default(3);
        });

        $grid->column('items', '.::.')->expand(function ($model) {
            $items = $model->item;
            foreach ($items as $key => $row) {
                $title[$key] = $row['title'];
                $status[$key] = $row['status'];
            }
            array_multisort(array_column($items, 'status'), SORT_DESC, $items);

            $new_items = [];
            // 遍历所有的items
            foreach ($items as $key => $item) {
                $status_str = '';
                $title_style = '';
                \Log::info(__METHOD__, $item);
                $status = $item['status'];
                switch ($status) {
                    case Todo::STATUS_UNDO:
                        $title_style = 'danger';
                        $status_str = Todo::STATUS_UNDO_NAME;
                        break;
                    case Todo::STATUS_DONE:
                        $status_str = Todo::STATUS_DONE_NAME;
                        $title_style = 'success';
                        break;
                    case Todo::STATUS_PROGRESS:
                        $title_style = 'warning';
                        $status_str = Todo::STATUS_PROGRESS_NAME;
                        break;
                }

                $title = $item['title'];
                $start_str = str_repeat('&nbsp;', 37);
                $second_str = str_repeat('&nbsp;', 8);
                if ($status == Todo::STATUS_DONE) {
                    $title = sprintf('<span>%s%s<span class="text-%s">%s<del>%s</del></span></span>', $start_str, $status_str, $title_style, $second_str, $title);
                } else if ($status == Todo::STATUS_PROGRESS) {
                    $title = sprintf('<span>%s%s<span class="text-%s">%s<u>%s</u></span></span>', $start_str, $status_str, $title_style, $second_str, $title);
                } else {
                    $title = sprintf('<span>%s%s<span class="text-%s">%s%s</span></span>', $start_str, $status_str, $title_style, $second_str, $title);
                }
                $new_items[] = [
                    'status_str' => $title
                ];
            };
            $str = str_repeat('&nbsp;', 32);
            return new Table([$str . 'status'], $new_items);
        })->width(50);

        $grid->column('deadline_at', 'date')->display(function ($deadline_at) {
            return date("m-d", strtotime($deadline_at));
        })->width(50);
        $grid->column('status', 'stat')->action(IsDone::class)->width(50);
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
            // 真假，不是该对象，更糟糕，用更差的衬托，偷换概念（我在紧张的思考），借代（量词，一口），反复，相关（死，擦伤），对偶，设问（地球之所以美丽是因为没放弃任何一种颜色），反语（乔丹太犹豫了），共同点（生活咖啡），夸张（空间上的夸张），对比（萌，凶），
            switch ($this->status) {
                case Todo::STATUS_UNDO:
                    $title_style = 'danger';
                    break;
                case Todo::STATUS_PROGRESS:
                    $title_style = 'warning';
                    break;
                case Todo::STATUS_DONE:
                    $title_style = 'success';
                    break;
                default:
                    $title_style = 'danger';
                    break;
            }
            if ($this->status == Todo::STATUS_DONE) {
                return sprintf('<p class="text-%s"><del>%s %s</del></p>', $title_style, $tags, $title);
            } else if ($this->status == Todo::STATUS_PROGRESS) {
                return sprintf('<p class="text-%s"><u>%s %s</u></p>', $title_style, $tags, $title);
            } else {
                return sprintf('<p class="text-%s">%s %s</p>', $title_style, $tags, $title);
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
        // $form->radioCard('type_id')->options($types);
        $form->text('title')->required();
        $form->date('deadline_at')->format('YYYY-MM-DD');
        // $tags = Tag::where('group', Todo::NAME)->pluck('name', 'id');
        // $form->listbox('tags', 'choose tags')->options($tags);
        // $form->starRating('importance');
        // $form->textarea('content');
        // $form->text('remark', 'remark');
        $form->table('item', function ($table) {
            $table->text('title');
            $table->radio('status')->options(
                [
                    Todo::STATUS_UNDO => Todo::STATUS_UNDO_NAME,
                    Todo::STATUS_PROGRESS => Todo::STATUS_PROGRESS_NAME,
                    Todo::STATUS_DONE => Todo::STATUS_DONE_NAME,
                ]
            )->default(Todo::STATUS_UNDO);
        });
        $form->radio('status')->options(
            [
                Todo::STATUS_UNDO => Todo::STATUS_UNDO_NAME,
                Todo::STATUS_PROGRESS => Todo::STATUS_PROGRESS_NAME,
                Todo::STATUS_DONE => Todo::STATUS_DONE_NAME,
            ]
        )->default(Todo::STATUS_UNDO);
        // $form->text('status');

        $form->saving(function (Form $form) {
            \Log::info(__METHOD__, ['request:', $form->item]);
        });
        return $form;
    }
}
