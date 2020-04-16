<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;


use App\Product;
use App\TopProduct;
use App\Cart;
use App\Category;
use Carbon\Carbon;
use App\Exceptions\Handler;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm() {
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
                                
        $top_products = TopProduct::with(['product'])
                            ->orderBy('created_at','desc')
                            ->get();
                        
        $session_id = session()->get( '_token' );
              
        $cart_count = Cart::where('session_id', $session_id)
                            ->count();

        return view('auth.passwords.email', compact('categories','top_products', 'cart_count', 'session_id'));
    }
}
