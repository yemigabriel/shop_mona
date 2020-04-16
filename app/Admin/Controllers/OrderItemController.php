<?php

namespace App\Admin\Controllers;

use App\OrderItem;
use App\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class OrderItemController extends Controller
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

            $content->header('Items Ordered');
            $content->description('Items Ordered');

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

            $content->header('Items Ordered');
            $content->description('Items Ordered');

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

            $content->header('Items Ordered');
            $content->description('Items Ordered');

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
        return Admin::grid(OrderItem::class, function (Grid $grid) {

            // $grid->id('ID')->sortable();
            $grid->order_identity('Order Number');

            // The third column shows the director field, which is set by the display($callback) method to display the corresponding user name in the users table
            $grid->product_id('Product')->sortable()->display(function($productId) {
                return Product::find($productId)->title;
            });
            $grid->quantity();
            $grid->price();

            $grid->created_at()->sortable();
            // $grid->updated_at();

            $grid->filter(function ($filter) {

                // Sets the range query for the created_at field
                $filter->between('created_at', 'Created Time')->datetime();
                $filter->equal('order_identity', 'Order Number');
            });

            
            $grid->disableCreation();
            $grid->disableRowSelector();
            // $grid->disableBatchDeletion();
            // $grid->disableExport();
            // $grid->actions('edit');
            $grid->disableActions();
            
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(OrderItem::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
