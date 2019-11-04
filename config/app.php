<?php
/**
 * Application configuration loader
 * Values are taken from the .env file at the root of the project
 * 
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Google Calendar configuration
    |--------------------------------------------------------------------------
    |
    | 
    |
    */
    'GOOGLE_API_KEY' => env('GOOGLE_API_KEY'),
    'GOOGLE_CAL_TMZ' => env('GOOGLE_CAL_TMZ'),
];
