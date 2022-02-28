<?php

namespace App\Admin\Controllers;

use App\Models\Type;
use App\Models\Joke;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;

class TestController extends Controller
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
        Admin::js('https://cdn.bootcss.com/vue/2.6.10/vue.min.js');

        return $content->row(function (Row $row) {
            $row->column(4, '<div class="info-box">
            <span class="info-box-icon bg-info"><i class="far fa-bookmark"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Bookmarks</span>
              <span class="info-box-number">41,410</span>
              <div class="progress">
                <div class="progress-bar bg-info" style="width: 70%"></div>
              </div>
              <span class="progress-description">
                70% Increase in 30 Days
              </span>
            </div>
          </div>');
            $row->column(8, function (Column $column) {
                $column->row('<div class="info-box">
                <span class="info-box-icon bg-info"><i class="far fa-bookmark"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Bookmarks</span>
                  <span class="info-box-number">41,410</span>
                  <div class="progress">
                    <div class="progress-bar bg-info" style="width: 70%"></div>
                  </div>
                  <span class="progress-description">
                    70% Increase in 30 Days
                  </span>
                </div>
              </div>');
                $column->row('<div class="info-box">
                <span class="info-box-icon bg-info"><i class="far fa-bookmark"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Bookmarks</span>
                  <span class="info-box-number">41,410</span>
                  <div class="progress">
                    <div class="progress-bar bg-info" style="width: 70%"></div>
                  </div>
                  <span class="progress-description">
                    70% Increase in 30 Days
                  </span>
                </div>
              </div>');
                $column->row(function (Row $row) {
                    $row->column(6, '<div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-bookmark"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Bookmarks</span>
                      <span class="info-box-number">41,410</span>
                      <div class="progress">
                        <div class="progress-bar bg-info" style="width: 70%"></div>
                      </div>
                      <span class="progress-description">
                        70% Increase in 30 Days
                      </span>
                    </div>
                  </div>');
                    $row->column(6, '<div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-bookmark"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Bookmarks</span>
                      <span class="info-box-number">41,410</span>
                      <div class="progress">
                        <div class="progress-bar bg-info" style="width: 70%"></div>
                      </div>
                      <span class="progress-description">
                        70% Increase in 30 Days
                      </span>
                    </div>
                  </div>');
                });
            });
        });
    }
}
