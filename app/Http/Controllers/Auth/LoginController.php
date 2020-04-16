<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;


use App\Product;
use App\TopProduct;
use App\Cart;
use App\Category;
use Carbon\Carbon;
use App\Exceptions\Handler;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm() {
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
                                
        $top_products = TopProduct::with(['product'])
                            ->orderBy('created_at','desc')
                            ->get();
                        
        $session_id = session()->get( '_token' );
              
        $cart_count = Cart::where('session_id', $session_id)
                            ->count();

        return view('auth.login', compact('categories','top_products', 'cart_count', 'session_id'));
    }
}
