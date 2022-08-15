<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Column;
use App\Admin\Extensions\Column\CodeWrapper;
use App\Admin\Extensions\Column\LabelWrapper;
use App\Admin\Extensions\Column\BadgeWrapper;
use App\Admin\Extensions\Column\LanguageWrapper;
use App\Admin\Extensions\Column\GradeWrapper;
use App\Admin\Extensions\Column\SentenceWrapper;
use App\Admin\Extensions\Column\ContentWrapper;

app('view')->prependNamespace('admin', resource_path('views/admin'));

Form::forget(['map', 'editor']);
Form::extend('largefile', \Encore\LargeFileUpload\LargeFileField::class);
Admin::css('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
Admin::css('/vendor/prism/prism.css');
Admin::js('/vendor/prism/prism.js');
Admin::script('Prism.highlightAll();');

Admin::favicon('https://i.ibb.co/60rG9Xs/cms-logo.png');


Column::extend('color', function ($value, $color) {
    return "<span style='color: $color'>$value</span>";
});

Column::extend('codeWrapper', CodeWrapper::class);
Column::extend('labelWrapper', LabelWrapper::class);
Column::extend('badgeWrapper', BadgeWrapper::class);
Column::extend('languageWrapper', LanguageWrapper::class);
Column::extend('gradeWrapper', GradeWrapper::class);
Column::extend('sentenceWrapper', SentenceWrapper::class);
Column::extend('contentWrapper', ContentWrapper::class);

Form::init(function (Form $form) {

    $form->tools(function (Form\Tools $tools) {
        $tools->append('<a class="btn btn-sm btn-default" onClick="javascript :history.back(-1);"><i class="glyphicon glyphicon-arrow-left"></i> Previous</a>');
    });

    $form->footer(function ($footer) {
        // 去掉`查看`checkbox
        $footer->disableViewCheck();
        // 去掉`继续编辑`checkbox
        $footer->disableEditingCheck();
        // 去掉`继续创建`checkbox
        $footer->disableCreatingCheck();
    });
});

Grid::init(function (Grid $grid) {

    $grid->filter(function (Grid\Filter $filter) {
        $filter->disableIdFilter();
    });

    // $grid->tools(function (Grid\Tools $tools) {
    // $tools->append('<a class="btn btn-sm btn-default" onClick="javascript :history.back(-1);"><i class="glyphicon glyphicon-arrow-left"></i> Previous</a>');
    // });
    $grid->disableRowSelector();
});
