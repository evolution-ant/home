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

Encore\Admin\Form::forget(['map', 'editor']);
Encore\Admin\Form::extend('largefile', \Encore\LargeFileUpload\LargeFileField::class);
Encore\Admin\Admin::css('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');

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

    $grid->tools(function (Grid\Tools $tools) {
        $tools->append('<a class="btn btn-sm btn-default" onClick="javascript :history.back(-1);"><i class="glyphicon glyphicon-arrow-left"></i> Previous</a>');
    });
    $grid->disableRowSelector();
});
