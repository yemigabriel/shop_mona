<?php

namespace App\Admin\Controllers;

use App\Category;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CategoryController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Categories');
            $content->description('All categories of products sold');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Categories');
            $content->description('Edit category');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('Categories');
            $content->description('Add new category');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Category::class, function (Grid $grid) {

            // $grid->id('ID')->sortable();
            $grid->column('name')->sortable();

            // The third column shows the director field, which is set by the display($callback) method to display the corresponding user name in the users table
            $grid->products('Products')->sortable()->display(function($products) {
                $count = count($products);
                return "<span class='label label-warning'>{$count}</span>";
            });

        
            // $grid->created_at();
            // $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Category::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Name')->rules('required|min:3');
            $form->text('alias', 'Url Slug')->rules('required|min:3'
            // , 
                // function($form) {
                //     // If it is not an edit state, add field unique verification
                //     if (!$id = $form->model()->id) {
                //         return 'unique:categories,alias';
                //     }
                // }
            );

            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');
        });
    }
}
