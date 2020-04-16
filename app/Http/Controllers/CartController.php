<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use Auth;
use DB;
use App\Product;
use App\TopProduct;
use App\Cart;
use App\Option;
use App\OptionValue;
use App\Order;
use App\Category;
use App\Shipping;
use App\ShippingInfo;
use Carbon\Carbon;
use App\Exceptions\Handler;

class CartController extends Controller
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
                                
        $top_products = TopProduct::with(['product'])
                            ->orderBy('created_at','desc')
                            ->get();
                        
        $session_id = session()->get( '_token' );
              
        $cart_count = Cart::where('session_id', $session_id)
                            ->count();

        $cart_items = Cart::with(['product'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();
        
        $cart_count = count($cart_items);
        
        $total_price = Cart::where('session_id', $session_id)
                            ->sum('total_amount');
                            // ->sum(function($col){ 
                            //         return $col->price * $col->qty; 
                            //     });


        $options = Option::where('published', 1)->get();

        $shipping_infos = ShippingInfo::all();
        $shipping = Shipping::with(['shipping_info'])->where('session_id', $session_id )->first();

        return view('cart.list', compact('cart_items','categories','total_price', 'cart_count', 'options', 'shipping_infos', 'shipping'));
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
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // add product to cart
        $session_id = session()->get( '_token' );
        $product_id = $id;
        $product = Product::where( 'id', $product_id )->first();
        if ( $product == null ) {
            return abort( 404 );
        }


        $option_value_id = request()->option_value_id;
         
        $cart = new Cart();


        if ( Cart::where( 'session_id', '=', $session_id )->exists() ) {

            //Check whether product exist if yes increase quantity
            $entry = Cart::where( [ 'session_id' => $session_id, 'product_id' => $product_id, 'option_value_id', $option_value_id ] )
                            // ->increment( 'qty', 1, ['product_name' => $product->title] );
                                ->first();
                            
            // $cart_entry = Cart::where([ 'session_id' => $session_id, 'product_id' => $product_id ])
            // ->update(['votes' => 1]);
            
            if (!$entry ) {
                $cart->session_id = $session_id;
                $cart->product_id = $product_id;
                $cart->product_name = $product->title;
                $cart->option_value_id = $option_value_id; 

                if ($product->discount_price) {
                    $cart->price = $product->discount_price;
                    $cart->total_amount = $product->discount_price;
                }
                else {
                    $cart->price = $product->original_price;
                    $cart->total_amount = $product->original_price;
                }
                $cart->qty = 1;
                $cart->save();
            }
            else {
                if ($product->discount_price) {
                    $entry->total_amount = $product->discount_price * ($entry->qty + 1);
                }
                else {
                    $entry->total_amount = $product->original_price * ($entry->qty + 1);
                }
                $entry->increment('qty', 1, ['product_id' => $product_id]);
                $entry->save();
            }
        } 
        else {
            $cart->session_id = $session_id;
            $cart->product_id = $product_id;
            $cart->product_name = $product->title;
            $cart->option_value_id = $option_value_id; 

            if ($product->discount_price) {
                $cart->price = $product->discount_price;
                $cart->total_amount = $product->discount_price;
            }
            else {
                $cart->price = $product->original_price;
                $cart->total_amount = $product->original_price;
            }
            $cart->qty = 1;
            $cart->save();
        }
        // First check whether the cart exist
        return redirect()->route( 'cart.index' );
        

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

    public function checkout()
    {
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
        $session_id = session()->get( '_token' );
                                
        $cart_items = Cart::with(['product','option_values'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();
        
        $cart_count = count($cart_items);


        $options = Option::where('published', 1)->get();
        
        return view('cart.checkout', compact('cart_items','categories', 'cart_count', 'options'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Cart::destroy($id);
        
        return redirect()->route( 'cart.index' );
    }

    public function addToCart($alias)
    {

        // add product to cart
        $session_id = session()->get( '_token' );
        $product = Product::where('alias', $alias)
                            ->first();
        if ( $product == null ) {
            return abort( 404 );
        }
        
        $product_id = $product->id;
        $option_value_id = request()->option_value_id;
        $num_product = (int) request()->num_product;
        $cart = new Cart();
                    
        if ( Cart::where( 'session_id', '=', $session_id )->exists() ) {

            //Check whether product exist if yes increase quantity
            $entry = Cart::where( [ 'session_id' => $session_id, 'product_id' => $product_id, 'option_value_id' => $option_value_id ] )
                            ->first();
            
            if (!$entry ) {
                $cart->session_id = $session_id;
                $cart->product_id = $product_id;
                $cart->product_name = $product->title;
                $cart->option_value_id = $option_value_id;

                if ($product->discount_price) {
                    $cart->price = $product->discount_price;
                    $cart->total_amount = $product->discount_price * $num_product ;
                }
                else {
                    $cart->price = $product->original_price;
                    $cart->total_amount = $product->original_price * $num_product;
                }
                
                $cart->qty = $num_product;
                
                $cart->save();
            }
            else {
                if ($product->discount_price) {
                    $entry->total_amount = $product->discount_price * ($entry->qty + $num_product);
                }
                else {
                    $entry->total_amount = $product->original_price * ($entry->qty + $num_product);
                }

                $entry->increment('qty', $num_product, ['product_id' => $product_id]);
                
                $entry->save();
            }
        } 
        else {
            $cart->session_id = $session_id;
            $cart->product_id = $product_id;
            $cart->product_name = $product->title;
            $cart->option_value_id = $option_value_id;

            if ($product->discount_price) {
                $cart->price = $product->discount_price;
                $cart->total_amount = $product->discount_price * $num_product; 
            }
            else {
                $cart->price = $product->original_price;
                $cart->total_amount = $product->original_price * $num_product;
            }
        
            $cart->qty = $num_product;
            
            $cart->save();
        }
        // First check whether the cart exist
        // return redirect()->route( 'cart.index' );


        $cart_items = Cart::with(['product'])
                    ->where('session_id', $session_id)
                    ->orderBy('created_at','desc')
                    ->get();
        // $cart_count = $cart_items->count();
                    
        $html = view('cart.cart-render', compact('cart_items'))->render();


        // $response = array(
        //   'status' => 'success',
        //   // 'msg' => $request->message,
        // );
        return $html; 

    }

    public function fetchCart()
    {
        $cart_items = Cart::with(['product'])
                    ->where('session_id', $session_id)
                    ->orderBy('created_at','desc')
                    ->get();

        return response()->json($cart_items);

    }

    public function addShipping(Request $request)
    {
        $session_id = session()->get( '_token' );
        $shipping_info_id = $request->shipping_info_id;

        // $shipping = Shipping::firstOrCreate(['session_id' => $session_id ], ['shipping_info_id' => $shipping_info_id, 'visitor' => $request()->ip()]);

        $shipping = Shipping::where('session_id', $session_id)->first();

        if ($shipping) {
            $shipping->shipping_info_id = $shipping_info_id;
            $shipping->save();
        } else {
            $shipping = new Shipping();
            $shipping->shipping_info_id = $shipping_info_id;
            $shipping->session_id = $session_id;
            $shipping->visitor = $request->ip();
            $shipping->save();
        }

        $shipping_infos = ShippingInfo::all();

        $cart_items = Cart::with(['product'])
                    ->where('session_id', $session_id)
                    ->orderBy('created_at','desc')
                    ->get();

        // $options = Option::where('published', 1)->get();


        $result = view('cart.cart-totals-render', compact('cart_items', 'shipping', 'shipping_infos'))->render();
                    

        // echo $session_id;//"yes";//response()->json($shipping);

        return $result;


    }

}
