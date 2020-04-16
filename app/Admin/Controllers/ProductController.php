<?php

namespace App\Admin\Controllers;

use App\Product;
use App\ProductImage;
use App\Category;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductController extends Controller
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

            $content->header('Products');
            $content->description('All products for sale');

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

            $content->header('Products');
            $content->description('Edit product');

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

            $content->header('Products');
            $content->description('Add new product');

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
        return Admin::grid(Product::class, function (Grid $grid) {

            // $grid->id('ID')->sortable();

            $grid->image()->image(config('devstudios.images.uploads.url'), 50, 50);

            // The second column shows the title field, because the title field name and the Grid object's title method conflict, so use Grid's column () method instead
            $grid->column('title')->sortable();

            // The third column shows the director field, which is set by the display($callback) method to display the corresponding user name in the users table
            $grid->category_id('Category')->sortable()->display(function($categoryId) {
                return Category::find($categoryId)->name;
            });

            // $grid->alias('Url Slug');
            // $grid->detail();
            $grid->original_price()->sortable();
            $grid->discount_price()->sortable();
            $grid->in_stock('In Stock')->display(function($in_stock) {
                return $in_stock == 1 ? 'yes' : 'no';
            });
            $grid->status('Published')->display(function($status) {
                return $status == 1 ? 'yes' : 'no';
            });


            $grid->created_at()->sortable();
            $grid->updated_at()->sortable();

            // The filter($callback) method is used to set up a simple search box for the table
            $grid->filter(function ($filter) {

                // Sets the range query for the created_at field
                $filter->between('created_at', 'Created Time')->datetime();
                $filter->like('title', 'Title');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Product::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('title', 'Title')->rules('required|min:3');
            $form->select('category_id','Category')->options(Category::all()->pluck('name', 'id'));
            // $form->text('category_id', 'Category')->rules('required');
            $form->text('alias', 'Url Slug')->rules('required|min:3');
            $form->textarea('detail', 'Short Description')->rules('required|min:3');
            $form->currency('original_price', 'Original Price')->symbol('N');
            $form->currency('discount_price', 'Discount Price')->symbol('N');
            $form->switch('in_stock', 'In Stock');
            $form->switch('status', 'Publish');
            $form->image('image', 'Image')->uniqueName()->removable();



            // // change upload path
            // $form->image('picture')->move('public/upload/image1/');

            // // use a unique name (md5(uniqid()).extension)
            // $form->image('picture')->uniqueName();

            // // specify filename
            // $form->image('picture')->name(function ($file) {
            // return 'test.'.$file->guessExtension();
            // });

            // $form->image($column[, $label]);

            // // Modify the image upload path and file name
            // $form->image($column[, $label])->move($dir, $name);

            // // Crop picture
            // $form->image($column[, $label])->crop(int $width, int $height, [int $x, int $y]);

            // // Add a watermark
            // $form->image($column[, $label])->insert($watermark, 'center');

            // // add delete button
            // $form->image($column[, $label])->removable();
            

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            $form->saved(function (Form $form) {
                if ($form->image) {
                    $product_image = $form->model()->image;
                    ProductImage::create([
                        'product_id' => $form->model()->id,
                        'image' => $form->model()->image,
                    ]);
                }

            });
        });
    }

           
    // protected function store()
    // {
    //     return $this->form()->store();
    // }


}
