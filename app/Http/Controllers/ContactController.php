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
use App\Contact;

use Mail;

use App\Mail\ContactMail;

use Carbon\Carbon;
use App\Exceptions\Handler;


class ContactController extends Controller
{

	public function create()
    {
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

        return view('layouts.contact', compact('categories', 'cart_count', 'session_id', 'cart_items', 'options'));
    }

    public function store(Request $request) {

    	$contact = Contact::create([
			    		'name' => $request->name,
			    		'email' => $request->email,
			    		'phone' => $request->phone,
			    		'message' => $request->message,
			    		'visitor' => $request->ip(),
			    	]);


        Mail::to("hello@shopmona.com.ng")->send(new ContactMail($contact));

    	// return redirect('/contact');
    	return redirect()->back()->with('message', 'Message successfully sent!');

    }
}
