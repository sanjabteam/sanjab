<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin routes prefix.
    |--------------------------------------------------------------------------
     */
    'route' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Array of all sanjab controllers.
    |--------------------------------------------------------------------------
     */
    'controllers' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | All locales availble for website.
    |--------------------------------------------------------------------------
    |
    | Array of locales. keys are locale ISO key and array values are title of locale to show to admins.
    |
     */
    'locales' => [
        'en' => 'English',
    ],

    /*
    |--------------------------------------------------------------------------
    | User login config.
    |--------------------------------------------------------------------------
     */
    'login' => [
        /**
         * Field inside \App\User model to use as username.
         */
        'username'  => 'email',

        /**
         * Username label that shows to admin in login page.
         */
        'title'     => 'Email',

        /**
         * Enable recaptcha for login.
         */
        'recaptcha' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Recaptcha information.
    |--------------------------------------------------------------------------
    |
    | Submit your app at https://www.google.com/recaptcha and add site-key and secret-key here.
    | It's recommneded to use .env file instead of modify this directly.
    | Sanjab using recaptcha v2.
    |
     */
    'recaptcha' => [
        /**
         * Recaptcha site key
         */
        'site_key'        => env('RECAPTCHA_SITE_KEY'),

        /**
         * Recaptcha secret key
         */
        'secret_key'      => env('RECAPTCHA_SECRET_KEY'),

        /**
         * Recaptcha will always passed on debug.
         */
        'ignore_on_debug' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme settings.
    |--------------------------------------------------------------------------
     */
    'theme' => [
        /*
        |--------------------------------------------------------------------------
        | Theme color.
        |--------------------------------------------------------------------------
        |
        | Possible values are: 'red','pink','purple','deep-purple','indigo','blue',
        | 'light-blue','cyan','teal','green','light-green','lime','yellow',
        | 'amber','orange','deep-orange','brown','grey','blue-grey'
        |
        */
        'color' => 'orange',

        /*
        |--------------------------------------------------------------------------
        | Footer text content.
        |--------------------------------------------------------------------------
        */
        'footer_note' => '&copy; <script>document.write(new Date().getFullYear())</script>,
                        made with <i class="material-icons">favorite</i> by Creative Tim.
                        Powered by Sanjab',

        /*
        |--------------------------------------------------------------------------
        | Footer links.
        |--------------------------------------------------------------------------
        |
        | Array of links in footer.
        | Example: [
        |    ['title' => 'title of link', 'link' => 'link URL']
        | ]
        |
        */
        'footer_links' => [
            ['title' => 'CREATIVE TIM', 'link' => 'https://www.creative-tim.com/'],
            ['title' => 'SANJAB', 'link' => 'https://amir9480.github.io/'],
        ]
    ]
];
