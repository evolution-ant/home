<?php

namespace App\Admin\Controllers;

use App\Models\Joke;
use App\Models\Type;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

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
        $grid->quickSearch('content', 'remark');
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->select('type_id', 'Type', Type::where('group', Joke::NAME)->pluck('name', 'id'));
        });
        // $grid->header(function ($query) {
        // $jokes = $query->select(DB::raw('count(jokes) as count, jokes'))
        //     ->groupBy('jokes')->get()->pluck('count', 'jokes')->toArray();
        // $doughnut = view('admin.chart.jokes', compact('jokes'));
        //     return new Box('性别比例');
        // });
        // $grid->footer(function ($query) {
        //     $data = $query->where('importance', 0)->sum('importance');
        //     return "<div style='padding: 10px;'>总收入 ： $data</div>";
        // });
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
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
                1 => '⭐️ ',
                2 => '⭐️ ⭐️',
                3 => '⭐️ ⭐️ ⭐️',
                4 => '⭐️ ⭐️ ⭐️ ⭐️',
                5 => '⭐️ ⭐️ ⭐️ ⭐️ ⭐️'
            ])->default(1);
            $create->text('remark', 'Remark');
        });

        $grid->column('type_id', 'Type')->display(function ($type_id) {
            $style = 'default';
            if ($type_id == 0) {
                return "";
            }
            $name = Type::find($type_id)->name;
            switch ($type_id % 5) {
                case 0:
                    $style = 'primary';
                    break;
                case 1:
                    $style = 'success';
                    break;
                case 2:
                    $style = 'info';
                    break;
                case 3:
                    $style = 'warning';
                    break;
                case 4:
                    $style = 'danger';
                    break;
                default:
                    $style = 'default';
                    break;
            }
            \Log::info($type_id);
            return "<span class='label label-$style'>$name</span> ";
        });
        $grid->content('Content')->width(600);
        $grid->tags()->pluck('name', 'id')->display(function ($tags) {
            $value = '';
            foreach ($tags as $id => $name) {
                $style = '';
                switch ($id % 5) {
                    case 0:
                        $style = 'default';
                        break;
                    case 1:
                        $style = 'primary';
                        break;
                    case 2:
                        $style = 'success';
                        break;
                    case 3:
                        $style = 'danger';
                        break;
                    case 4:
                        $style = 'warning';
                        break;
                    case 5:
                        $style = 'info';
                        break;
                    default:
                        $style = 'default';
                        break;
                }
                $value = $value . "<span class='label label-$style'>$name</span> ";
            }
            return $value;
        });
        // $grid->column('reading_times')->display(function ($reading_times) {
        //     return "<span class='label label-default'>$reading_times</span>";
        // })->sortable();

        $grid->importance()->display(function ($importance) {
            $html = "<i class='fa fa-star' style='color:#ff8913'></i>";
            if ($importance < 1) {
                return '';
            }
            return join('&nbsp;', array_fill(0, min(5, $importance), $html));
        })->sortable();
        $grid->remark('Remark')->width(300)->color('red');

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
        // $form->row(function ($row) {
        //     $types = Type::where('group', Joke::NAME)->pluck('name', 'id');
        //     $row->radio('type_id', "type")->options($types);
        // });
        $types = Type::where('group', Joke::NAME)->pluck('name', 'id');
        $form->radio('type_id', "type")->options($types);
        $form->textarea('content', 'content')->required();
        $tags = Tag::where('group', Joke::NAME)->pluck('name', 'id');
        $form->listbox('tags', 'choose tags')->options($tags);
        $form->starRating('importance');
        $form->text('remark', 'remark');
        return $form;
    }
}
