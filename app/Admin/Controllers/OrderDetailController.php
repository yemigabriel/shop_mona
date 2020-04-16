<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Controllers\ModelForm;

use App\Product;
use App\Category;
use App\Order;
use App\OrderItem;


class OrderDetailController extends Controller
{
    public function index($id)
    {
        return Admin::content(function (Content $content) use ($id){

            $order = Order::find($id);
            $content->header('Order #'.$order->order_identity.' - (N'.$order->gross_price.')');
            $content->description('Items ordered for Order #'.$order->order_identity);

            $content->body($this->grid($id));
        });
    }

    protected function grid($id)
    {
        return Admin::grid(OrderItem::class, function (Grid $grid) use ($id) {
            $grid->model()->where('order_id',$id);
            $grid->id('ID')->sortable();
            $grid->column('product.title','Product');
            $grid->price();
            $grid->quantity();
            $grid->order_identity('Order Number');

            $grid->created_at('Date');
            // $grid->updated_at();

            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->disableActions();

        });
    }

}
