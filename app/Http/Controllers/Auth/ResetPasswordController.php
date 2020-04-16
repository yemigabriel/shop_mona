<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;


use App\Product;
use App\TopProduct;
use App\Cart;
use App\Category;
use Carbon\Carbon;
use App\Exceptions\Handler;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null) {
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
                                
        $top_products = TopProduct::with(['product'])
                            ->orderBy('created_at','desc')
                            ->get();
                        
        $session_id = session()->get( '_token' );
              
        $cart_count = Cart::where('session_id', $session_id)
                            ->count();

        return view('auth.passwords.reset', compact('categories','top_products', 'cart_count', 'session_id'))->with(['token' => $token, 'email' => $request->email]);

    }
}
