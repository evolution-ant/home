<?php

namespace App\Admin\Controllers;

use App\Models\Sentence;
use App\Models\Type;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Widgets\Form as WForm;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use App\Admin\Actions\Code\Restore;
use App\Admin\Actions\Sentence\LikeSentence;

class SentenceController extends Controller
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
            ->header('Sentence')
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

        $grid = new Grid(new Sentence);
        $grid->model()->orderBy('importance', 'desc')->orderBy('updated_at', 'desc');

        $grid->actions(function ($actions) {
            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
            $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
            $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/types?&_selector%5Bgroup%5D=' . Sentence::NAME . '" class="btn btn-success btn-sm" role="button">Type</a>');
            $tools->append('<a href="/admin/tags?&_selector%5Bgroup%5D=' . Sentence::NAME . '" class="btn btn-danger btn-sm" role="button">Tag</a>');
        });
        $grid->enableHotKeys();
        $grid->quickSearch(function ($model, $query) {
            $model->orWhere('content', 'like', "%{$query}%");
        });
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->selectOne('type_id', 'Type', Type::where('group', Sentence::NAME)->pluck('name', 'id'));
            $selector->select('tags', 'Tags',  Tag::where('group', Sentence::NAME)->pluck('name', 'id'), function ($query, $value) {
                $sentence_ids = [];
                foreach ($value as $id) {
                    $tmp_sentences = DB::table("sentence_tag")->where('tag_id', $id)->get();
                    foreach ($tmp_sentences as $tmp_sentence) {
                        array_push($sentence_ids, $tmp_sentence->sentence_id);
                    }
                }
                $query->whereIn('id', $sentence_ids);
            });
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 3, function ($filter) {
                $filter->equal('type', 'Type')->select(function () {
                    $query = Type::where('group', Sentence::NAME)->pluck('name', 'id');
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
            $tags = Tag::where('group', Sentence::NAME)->pluck('name', 'id');
            $create->multipleSelect('tags', 'Tags')->options($tags);
            $create->text('remark', 'Remark');
        });

        // created_at 和 updated_at 列的显示格式
        $grid->column('created_at')->display(function ($created_at) {
            return date("Y-m-d", strtotime($created_at));
        });
        $grid->column('content')->expand(function ($model) {
            $str = '';
            $translations = $model->translations;
            $country = '';
            //如果 language 里包含 'ZH' 或 'zh',则country为 🇨🇳 ，否则为 🇺🇸
            if (strpos($model->language, 'ZH') !== false || strpos($model->language, 'zh') !== false) {
                $country = '';
            } else {
                $country = '';
            }
            if ($translations) {
                // $translations 放在 h3 里
                $str .= sprintf('%s <h3>%s</h3><br>', $country, $translations);
            }
            // $str 左对齐居中显示
            return '<div style="text-align:center;">' . $str . '</div>';
        });
        // 展示 grade 字段
        $grid->column('importance')->display(function ($grade, $column) {
            return $column->gradeWrapper(Sentence::NAME, $grade);
        });
        $grid->column('like')->action(LikeSentence::class);
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
        $show = new Show(Sentence::findOrFail($id));

        $show->id('ID');
        $show->content('content');
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
        $form = new Form(new Sentence);
        $types = Type::where('group', Sentence::NAME)->pluck('name', 'id');
        $form->radioCard('type_id', "type")->options($types);
        $form->textarea('content')->required();
        $form->textarea('translations')->required();

        $tags = Tag::where('group', Sentence::NAME)->pluck('name', 'id');
        $form->listbox('tags', 'choose tags')->options($tags);
        $form->text('remark', 'remark');

        return $form;
    }
}
