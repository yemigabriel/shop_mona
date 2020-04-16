<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use App\Http\Requests;
use App\User;
use Auth;
use App\Product;
use App\Slide;
use App\Blog;
use App\TopProduct;
use App\Cart;
use App\Option;
use App\OptionValue;
use App\Category;
use Carbon\Carbon;
use App\Exceptions\Handler;

class HomeController extends Controller
{
    //

    public function index()
    {
        $categories = Category::with(['products'])
                                ->orderBy('name','asc')
                                ->get();
                                
        $featured_products = TopProduct::with(['product'])
                            ->orderBy('created_at','desc')
                            ->take(8)
                            ->get();
                        
        $session_id = session()->get( '_token' );
              
        $cart_count = Cart::where('session_id', $session_id)
                            ->count();

        $cart_items = Cart::with(['product'])
                            ->where('session_id', $session_id)
                            ->orderBy('created_at','desc')
                            ->get();

        $slides = Slide::all();

        // instagram
        $client = new Client;
        $url = sprintf('https://api.instagram.com/v1/users/self/media/recent/?access_token='.config('devstudios.social.instagram.token').'&count=10');
        // $response = $client->get($url);

        $results = [];// json_decode($response->getBody(), true)["data"];

        // foreach ($results as $result) {
        //     return $result["images"]["standard_resolution"];
        // }

        $options = Option::where('published', 1)->get();

        return view('welcome', compact('categories','featured_products', 'cart_count', 'session_id', 'cart_items', 'slides', 'results', 'options'));
    }

    public function search(Request $request)
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

        $query = $request->search;

        $products = Product::where('title','like', '%'.$query.'%')
                            ->orderBy('created_at','desc')
                            ->paginate(24);


        $options = Option::where('published', 1)->get();

        return view('search', compact('categories','top_products', 'cart_count', 'session_id','products','query', 'options'));
    }

    

}
