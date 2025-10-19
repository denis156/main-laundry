<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Customer Service Phone Number
    |--------------------------------------------------------------------------
    |
    | This value is the phone number for customer service contact. It will be
    | used throughout the application where customer service contact
    | information needs to be displayed to users or customers.
    |
    */

    'phone' => env('SOSMED_PHONE', '+6281524089375'),

    /*
    |--------------------------------------------------------------------------
    | Customer Service Email Address
    |--------------------------------------------------------------------------
    |
    | This value is the email address for customer service contact. It will be
    | used for customer support inquiries and communication throughout the
    | application where email contact information is needed.
    |
    */

    'email' => env('SOSMED_EMAIL', 'mainlaundry@gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | Instagram Profile URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL to the application's Instagram profile. It can be
    | used to link to your social media presence from the application. Leave
    | it null if you don't have an Instagram account yet.
    |
    */

    'instagram' => env('SOSMED_INSTAGRAM'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Repository URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL to the application's GitHub repository. It can be
    | used for attribution, open source credits, or linking to the project's
    | source code repository from within the application.
    |
    */

    'github' => env('SOSMED_GITHUB', 'https://github.com/denis156/main-laundry'),

];
