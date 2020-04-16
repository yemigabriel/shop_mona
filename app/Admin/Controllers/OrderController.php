<?php

namespace App\Admin\Controllers;

use App\Order;
use App\OrderItem;
use App\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Admin\Extensions\Tools\ShowSelected;

use Encore\Admin\Widgets\Collapse;

// use App\Admin\Extensions\Tools\UserGender;
// use App\Http\Controllers\Controller;
// use App\Models\ChinaArea;
// use App\Models\User;
// use Encore\Admin\Form;
// use Encore\Admin\Grid;
// use Encore\Admin\Facades\Admin;
// use Encore\Admin\Controllers\ModelForm;
// use Encore\Admin\Layout\Content;
// use Encore\Admin\Widgets\Table;
// use Illuminate\Support\Facades\Request;


class OrderController extends Controller
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

            $content->header('Orders');
            $content->description('All customer orders');

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

            $content->header('Orders');
            $content->description('Order');

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

            $content->header('Orders');
            $content->description('All customer orders');

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
        return Admin::grid(Order::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->model()->with(['order_items','order_items.product'])->orderBy('created_at', 'desc');
            
            $grid->order_identity('Order Number')->display(function ($order_identity) {
                return "<a href='/admin/order/{$this->id}'>{$order_identity}</a>";
            });
            // $grid->column('order_items')->display(function () {
            //     // $order_items = array_only($this->order_items, ['product_id', 'price', 'quantity']);
            //     // return new Table([], $order_items);

            //     // $collapse = new Collapse();

            //     // $collapse->add('Bar', 'xxxxx');
            //     // // $collapse->add('Orders', new Table());

            //     // return $collapse->render();

            // }, 'Detail');
            // $grid->order_items('Order Items')->display(function($order_items) {
            //     $count = count($order_items);
            //     // return "<span class='label label-warning'>{$count}</span>";
            //     return new Table([],$order_items);
            // });

            // $grid->column('orderk_items')->display(function () {
            //     // $profile = array_only($this->order_items, ['order_identity']);
            //     // return new Table([], $profile);

            //     $headers = ['Keys', 'Values'];
            //     $rows = [
            //     'name'   => 'Joe',
            //     'age'    => 25,
            //     'gender' => 'Male',
            //     'birth'  => '1989-12-05',
            //     ];

            //     $table = new Table($headers, $rows);

            //     return $table->render();
            // });


            $grid->gross_price('Amount (N)');
            $grid->payment_id('Payment Method')->display(function($payment_id) {
                switch ($payment_id) {
                    case 1: 
                        return "<span class='label label-primary'>Bank Deposit</span>";
                        break;
                    case 2:
                        return "<span class='label label-primary'>Cash On Delivery</span>";
                        break;
                    case 3:
                        return "<span class='label label-primary'>Debit Card</span>";
                        break;
                }
            });
            
            $grid->status('Status')->display(function($status) {
                switch ($status) {
                    case 0: 
                        return "<span class='label label-danger'>Incomplete</span>";
                        break;
                    case 1:
                        return "<span class='label label-default'>Complete</span>";
                        break;
                    case 2:
                        return "<span class='label label-warning'>Processing</span>";
                        break;
                    case 3:
                        return "<span class='label label-primary'>Delivered</span>";
                        break;
                }
            });
            $grid->name('Customer');
            $grid->address('Address');
            $grid->phone('Phone');
            $grid->email('Email');

            // The third column shows the director field, which is set by the display($callback) method to display the corresponding user name in the users table
            // $grid->product_id('Product')->sortable()->display(function($product_id) {
            //     return Product::find($product_id)->name;
            // });


            $grid->created_at()->sortable();
            
            $grid->filter(function ($filter) {

                // Sets the range query for the created_at field
                $filter->between('created_at', 'Created Time')->datetime();
                $filter->equal('order_identity', 'Order Number');
                $filter->like('name', 'Customer');
                $filter->like('address', 'Address');
                $filter->like('phone', 'Phone');
                $filter->like('email', 'Email');
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                // $actions->disableEdit();
                // the array of data for the current row
                $actions->row;

                // gets the current row primary key value
                $actions->getKey();

            });

            $grid->disableCreateButton();
            $grid->disableRowSelector();
            // $grid->disableBatchDeletion();
            // $grid->disableExport();
            // $grid->disableActions();

            // $grid->tools(function ($tools) {
            //     // $tools->append(new Trashed());
            //     $tools->batch(function (Grid\Tools\BatchActions $batch) {
            //         // $batch->add('Restore', new RestorePost());
            //         // $batch->add('Release', new ReleasePost(1));
            //         // $batch->add('Unrelease', new ReleasePost(0));
            //         $batch->add('Show selected', new ShowSelected());
            //     });
            // });

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Order::class, function (Form $form) {


            // $form->disableDelete();
            // $form->disableCreateButton();
            // $form->disableSubmit();
            // $form->disableActions();
            $form->disableReset();
            
            $form->display('order_identity', 'Order Number');

            $form->select('status','Status')->options(['2' => 'Processing', '3' => 'Delivered']);
            
            // $form->hasMany('order_items', function (Form\NestedForm $form) {
                
            //         $form->display('product_id');
            //         $form->display('price');
            //         $form->display('quantity');
            //         // $form->datetime('created_at');
            // });
    
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    /**
     * Show interface.
     *
     * @param $id
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Items Ordered');
            $content->description('Items in this order');

            $content->body($this->grid()->detail($id));
        });
    }

    protected function detail($id)
    {
        return Admin::grid(OrderItem::class, function (Grid $grid) {
            
            // $form->disableSubmit();
            // $form->disableDelete();

            // $form->display('order_identity', 'Order Number');

            // // $form->select('status','Status')->options(['2' => 'Processing', '3' => 'Delivered']);
            
            // $grid->hasMany('order_items', function (Form\NestedForm $form) {
                
            //     $form->disableDelete();
            //     $form->display('product_id');
            //     $form->display('price');
            //     $form->display('quantity');
            //     // $form->datetime('created_at');
            // });


            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');

            // $grid->model()->with(['order_items','order_items.product'])->orderBy('created_at', 'desc');
            
            $grid->order_identity('Order Number');

            // $grid->product_id('Product')->sortable()->display(function($productId) {
            //     return Product::find($productId)->alias;
            // });
            $grid->quantity('Quantity');
            $grid->price('Price');
            
        });
    }
}
