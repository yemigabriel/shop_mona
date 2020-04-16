<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;
use DB;
use App\Product;
use App\TopProduct;
use App\Category;
use App\Cart;
use App\Option;
use App\OptionValue;
use App\Order;
use App\OrderItem;
use Carbon\Carbon;
use App\Exceptions\Handler;

class OrderController extends Controller
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
        // if ($request->session()->exists('_token')) {
        //     //
        // }
        $session_id = session()->get( '_token' );
                                
        $cart_items = Cart::with(['product'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();
        
        $cart_count = count($cart_items);

        $order = Order::where( [ 'session_id' => $session_id ] )->orderBy('created_at','desc')->first();
        
        $options = Option::where('published', 1)->get();

        if($order!=null) {
            // session()->forget('_token'); 
            return view('order.success', compact('cart_items','categories', 'cart_count','order', 'options'));
        } else {
            return redirect('/');
        }
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
        $session_id   = session()->get( '_token' );
        $order = Order::where( [ 'session_id' => $session_id ] )->first();
        $order->status = 1; //completed order.
        if ($order->save()) {
            Cart::where( 'session_id', $session_id )->delete();
            return redirect('/order');
            // echo 'eh '.$order;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
