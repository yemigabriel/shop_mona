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
use App\Shipping;
use App\ShippingInfo;
use App\Order;
use App\OrderItem;
use App\Transaction;


use Mail;

use App\Mail\SuccessfulOrder;
use App\Mail\SuccessfulOrderAdmin;



use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Exceptions\Handler;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    // protected $paystack_url = 'http://localhost:8000/payment';
    protected $paystack_url = 'https://shopmona.com.ng/payment';
    protected $paystack_secret_key = ''
    protected $paystack_public_key = '';

    public function index()
    {
        //
    }

    public function create()
    {
        // session()->flush();
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
        $session_id = session()->get( '_token' );
                                
        $cart_items = Cart::with(['product'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();
        
        $cart_count = count($cart_items);
        $user = Auth::user();

        $total_price = Cart::where('session_id', $session_id)
                            ->sum('total_amount');
        $user = Auth::user();


        $options = Option::where('published', 1)->get();

        $shipping_infos = ShippingInfo::all();
        $shipping = Shipping::with(['shipping_info'])->where('session_id', $session_id )->first();

        
        return view('cart.checkout', compact('cart_items','categories', 'cart_count','user','total_price', 'options', 'shipping_infos', 'shipping'));
    }

    public function checkout(Request $request) {
        $session_id   = session()->get( '_token' );
        $user_id = 0;
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        }
        $user_name = $request->name; //$request->firstname.' '.$request->lastname; 
        $user_email = $request->email; 
        $user_phone = $request->phone; 
        $user_address = $request->address; 
        // $request->flash();
        
        $order_number = substr( str_shuffle( "0123456789abcdefghijklmnopqrstuvwxyz" ), 0,
            4 ) . '-' . substr( str_shuffle( "0123456789abcdefghijklmnopqrstuvwxyz" ), 0,
            4 ) . '-' . substr( str_shuffle( "0123456789abcdefghijklmnopqrstuvwxyz" ), 0, 
            4 ) . '-' . $user_phone;

        $order_number = strtoupper( $order_number );

        $entries = Cart::where( [ 'session_id' => $session_id ] )->get();
        $total = 0;
        foreach ( $entries as $entry ) {
            $total += floatval( $entry['total_amount'] );
        }

            DB::beginTransaction();
            try {
                if (Order::where( 'session_id', '=', $session_id )->exists()) {
                    $order = Order::where( 'session_id', '=', $session_id )->first();
                    $order->user_id = $user_id;
                    $order->name = $user_name;
                    $order->phone = $user_phone;
                    $order->email = $user_email;
                    $order->address = $user_address;
                    $order->save();

                    //add shipping
                    $shipping = Shipping::where('session_id', $session_id)->first();
                    $shipping->order_id = $order->id;
                    $shipping->save();

                    foreach ( $entries as $entry ) {

                        OrderItem::updateOrCreate( [
                            'order_identity' => $order->order_identity,
                            'order_id'       => $order->id,
                            'product_id'     => $entry->product_id,
                            'option_value_id' => $entry->option_value_id,
                            'quantity'       => $entry->qty,
                            'price'          => $entry->total_amount,
                        ] );
                    }
                }
                else {
                    $order = Order::create( [
                            'order_identity'        => $order_number,
                            'session_id'            => $session_id,
                            'gross_price'           => $total,
                            'status'                => 0,
                            // 'payment_type_id'       => payment type id...
                            'user_id'               => $user_id,
                            'name'                  => $user_name,
                            'phone'                 => $user_phone,
                            'email'                 => $user_email,
                            'address'               => $user_address,
                            ] );


                    //add shipping
                    $shipping = Shipping::where('session_id', $session_id)->first();
                    $shipping->order_id = $order->id;
                    $shipping->save();

                    foreach ( $entries as $entry ) {

                        OrderItem::create( [
                            'order_identity' => $order_number,
                            'order_id'       => $order->id,
                            'product_id'     => $entry->product_id,
                            'option_value_id' => $entry->option_value_id,
                            'quantity'       => $entry->qty,
                            'price'          => $entry->total_amount,
                        ] );
                    }

                }

                //Reset Cart after order submission
                // Cart::where( 'session_id', $session_id )->delete();
                DB::commit();

            } catch ( Exception  $ex ) {
                DB::rollBack();
                print_r( $ex->getMessage() );
                echo "failed o... why? =>".  $ex->getMessage();
            }

            // return redirect( $paystack_url ); //redirect( '/payment' );
            //get shipping
            $shipping = Shipping::where( [ 'session_id' => $session_id ] )->first();
            $total_with_shipping = $total + $shipping->shipping_info()->first()->cost;
            session(['order_number' => $order_number]);

            
            
            $this->initialize_paystack($user_email, $total_with_shipping);


    }

    public function initialize_paystack($email, $total)
    {
        $url = 'https://api.paystack.co/transaction/initialize';

        $client = new Client();

        //add order_no to session
        
        $reference = substr(base64_encode($email), 0, 10).$this->clean(Carbon::now());
        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->paystack_secret_key,
                'Content-Type: application/json',
            ],
            'form_params' => [
                'email' => $email, 
                'amount' => 100 * $total,
                'reference' => $reference,
                // 'channels' => ['card'],
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        return redirect()->to($result['data']['authorization_url'])->send();
    }

    public function callback(Request $request) 
    {
        // do verify transaction here to obtain transaction details before saving to db and clearing loan...
        $secret = $this->paystack_secret_key;
        $reference = $request['reference'];
        $url = 'https://api.paystack.co/transaction/verify/'.$reference;
        $client = new Client();

        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$secret,
                'Content-Type: application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200 && $response->getReasonPhrase() == 'OK') {


        $order_number = session('order_number', $default = null);

        //link order with shipping
        // $order = Order::where('order_identity', $order_number)->first();

        // // $session_id = session()->get( '_token' );
        // $shipping = Shipping::where('session_id', $session_id)->first();
        // return $session_id;// $shipping->id;
        // $shipping->order_id = $order->id;
        // $shipping->save();

        // echo $response->json();
        $result = json_decode($response->getBody(), true);
        //get transaction details
        $amount = $result['data']['amount'];
        $reference = $result['data']['reference'];
        $channel = $result['data']['channel'];
        $ip_address = $result['data']['ip_address'];
        $card_type = $result['data']['authorization']['card_type'];
        $last4 = $result['data']['authorization']['last4'];
        $bank = $result['data']['authorization']['bank'];
        $country_code = $result['data']['authorization']['country_code'];
        $customer_code = $result['data']['customer']['customer_code'];



        //save transaction details
        $transaction = new Transaction();
        $transaction->order_identity = $order_number;
        $transaction->customer_code = $customer_code;
        $transaction->amount = $amount / 100;
        $transaction->reference = $reference;
        $transaction->channel = $channel;
        $transaction->bank = $bank;
        $transaction->card_type = $card_type;
        $transaction->last4 = $last4;
        $transaction->country_code = $country_code;
        $transaction->ip_address = $ip_address;
        $transaction->save();


        $this->sendEmailToBuyer($order_number);

        //return to success page.
        
        session()->flush();

        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
                        
        $session_id = session()->get( '_token' );

        $cart_count = Cart::where('session_id', $session_id)
                            ->count();


        $cart_items = Cart::with(['product'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();

        $options = Option::where('published', 1)->get();



        return view('cart.success', compact('order_number', 'categories', 'cart_items', 'cart_count', 'options'));

        }
        else {
            echo 'transaction not successfully completed';
        }
    }

    public function sendEmailToBuyer($order_number)
    {
        // session()->flush();
        // return $order_number;
        $order = Order::where('order_identity',$order_number)
                        ->first();

        //send email to user
        $email = $order->email;
        $customer_name = $order->name;

        Mail::to($email)->send(new SuccessfulOrder($order));
        Mail::to("hello@shopmona.com.ng")->send(new SuccessfulOrderAdmin($order));

        // return new SuccessfulOrder($order);


    }


    public function paymentForm() {
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
        $session_id = session()->get( '_token' );
            
        $cart_items = Cart::where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();

        $cart_count = count($cart_items);
        $user = Auth::user();

        $total_price = Cart::where('session_id', $session_id)
                            ->sum('price');


        $options = Option::where('published', 1)->get();

        return view('cart.payment_type', compact('cart_items','categories', 'cart_count','user','total_price', 'options'));

    }

    public function paymentType(Request $request) {
        $session_id   = session()->get( '_token' );
        $order = Order::where( [ 'session_id' => $session_id ] )->first();
        $order->payment_id = $request->payment_id;
        $order->save();

        return redirect('/review');
    }

    public function review() {

        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
        // if ($request->session()->exists('_token')) {
        //     //
        // }
        $session_id = session()->get( '_token' );
                                
        $cart_items = Cart::where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();
        
        $cart_count = count($cart_items);
                
        $total_price = Cart::where('session_id', $session_id)
                            ->sum('price');

        $user = Auth::user();


        $options = Option::where('published', 1)->get();

        return view('cart.review', compact('cart_items','categories','total_price', 'cart_count','user', 'options'));
    }

    public function finishOrder(Request $request) {

    }


    public function clean($string) {
       $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
       $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

       return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }



}
