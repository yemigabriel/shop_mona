<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'title' => env('APP_NAME', 'Shop Mona'),
    'developer' => [
        'name' => 'Dev Studios',
        'email' => 'hello@devstudios.ng',
        'website' => 'http://DevStudios.ng',
    ],
    'meta' => [
        'author' => 'Dev Studios',
        'keywords' => 'Shop Mona, Online Store, dresses, wrap dresses, jumpsuits, blouses, pants, sets, mona, nigeria, lagos, clothes',
        'description' => 'Shop Mona, clothes for every occassion',
    ],
    'logo' => 'images/logo.png',
    'logo-small' => 'images/logo.png',

    'theme' => 'default',

    'themes' => [
        'default' => 'default',
        'beautify' => 'beautify',
    ],

    'contact' => [
        'email' => 'hello@shopmona.com.ng',
        'phone' => '+2349068303781',
        'phone-text' => '+(234) 906 830 3781',
    ],

    'social' => [
        'instagram' => [
            'url' => 'https://www.instagram.com/shopmona_/',
            'account' => 'shopmona_',
            'token' => '7117053824.1677ed0.c54de49d60f7462580bf130ef13abff2',
        ],
        'twitter' => [
            'url' => 'https://twitter.com/shopmona__',
            'account' => 'shopmona_',
            'token' => '',
        ],
          
    ],

    'images' => [
        'logo' => 'images/logo.png',
        'logo-small' => 'images/logo-small.png',
        'uploads' => [
            'path' => 'images/uploads/',
            'url' => env('APP_URL', 'http://shopmona.com.ng').'/images/uploads/'
        ],
        'slides' => 'images/slides/',
    ],

    'disclaimer' => 'disclaimer',



    

];
