<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;
// use Meta;
use App\Product;
use App\Category;
use App\Size;
use App\Option;
use App\OptionValue;
use App\Cart;
use Carbon\Carbon;
use App\Exceptions\Handler;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
                                
        $products = Product::orderBy('created_at','desc')
                            ->paginate(12);

        $session_id = session()->get( '_token' );

        $cart_count = Cart::where('session_id', $session_id)
                            ->count();

        $cart_items = Cart::with(['product'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();

        $sizes = Size::all();
        $options = Option::where('published', 1)->get();

        return view('product.list', compact('categories','products', 'cart_count', 'cart_items', 'options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($alias)
    {
        $categories = Category::all();
         
        $session_id = session()->get( '_token' );

        $cart_count = Cart::where('session_id', $session_id)
                            ->count();

        $cart_items = Cart::with(['product'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();

        $sizes = Size::all();

        $product = Product::where('alias', $alias)
                            ->first();
        if ($product == null) {
            abort(404);
        }
        // Meta::meta('title', $product->title.' - '.config('devstudios.title') );
        // Meta::meta('description', $product->detail);
        
        //TODO: sub categories

        $related_products = Product::where('category_id', $product->category_id)
                            ->whereNotIn('id', [$product->id])
                            ->where('status', 1)
                            ->orderBy('created_at','desc')
                            ->take(6)
                            ->get();
        if ($related_products->count() < 3) {
            $related_products = Product::whereNotIn('id', [$product->id])
                            ->where('status', 1)
                            ->orderBy('created_at','desc')
                            ->take(4)
                            ->get();
        }

        $options = Option::where('published', 1)->get();

        

        return view('product.detail', compact('categories','product','related_products','cart_count', 'cart_items', 'options'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
