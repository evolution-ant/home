<?php

namespace App\Admin\Controllers;

use App\Models\Wisesaying;
use App\Models\Type;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;
use App\Admin\Actions\Wisesaying\Restore;
use App\Admin\Actions\Wisesaying\LikeBook;
use Predis\Command\PubSubPublish;

class WisesayingController extends Controller
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
            ->header('Wisesaying')
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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new Wisesaying);
        $grid->model()->orderBy('updated_at', 'desc');
        $grid->disableActions();
        $grid->actions(function ($actions) {
            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
            $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
            $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<a href="/admin/types?&_selector%5Bgroup%5D=wisesayings" class="btn btn-success btn-sm" role="button">Type</a>');
            $tools->append('<a href="/admin/tags?&_selector%5Bgroup%5D=wisesayings" class="btn btn-danger btn-sm" role="button">Tag</a>');
            $tools->append('<a href="/admin/wisesayings" class="btn btn-warning btn-sm" role="button">Clear</a>');
        });
        $grid->disableCreateButton();
        $grid->enableHotKeys();
        $grid->quickSearch(function ($model, $query) {
            $model->where('en_content', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%");
        });

        $grid->filter(function (Grid\Filter $filter) {
            // 查询 Wisesaying 表中的 author 字段值，并去重
            $filter->disableIdFilter();
            $filter->scope('trashed', '回收站')->onlyTrashed();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('author');
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('topic');
            });
        });
        $grid->column('author')->display(function ($author) {
            if ($author == null) {
                return '';
            }
            $style = 'default';
            switch (strlen($author) % 6) {
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
                case 5:
                    $style = 'default';
                    break;
                default:
                    $style = 'default';
                    break;
            }
            // 拼接 badge 样式和 a href标签
            $filter_url = sprintf('/admin/wisesayings?author=%s', $author);
            $url_str = '<a href="' . $filter_url . '" class="label label-' . $style . '">' . $author . '</a>';
            return $url_str;
        });
        $grid->column('topic')->display(function ($topic) {
            if ($topic == null) {
                return '';
            }
            $style = 'default';
            switch (strlen($topic) % 6) {
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
                case 5:
                    $style = 'default';
                    break;
                default:
                    $style = 'default';
                    break;
            }
            // 拼接 badge 样式和 a href标签
            $filter_url = sprintf('/admin/wisesayings?topic=%s', $topic);
            $url_str = '<a href="' . $filter_url . '" class="badge label-' . $style . '">' . $topic . '</a>';
            return $url_str;
        });
        $grid->column('content1')->display(function ($content, $column) {
            return '<div class="well">
            <p>' . $this->en_content . '</p>
                <p>' . $this->content . '</p>
            </div>';
        });
        $grid->column('content')->display(function ($content, $column) {
            // 编辑按钮
            $edit_str = '编辑';
            return $edit_str;
        })->editable();
        // budge 显示
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
        $show = new Show(Wisesaying::findOrFail($id));
        $show->id('ID');
        $show->content('content');
        $show->remark('remark');
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
        $form = new Form(new Wisesaying);
        $form->text('content');
        return $form;
    }
}
