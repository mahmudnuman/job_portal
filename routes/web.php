<?php

use Illuminate\Support\Facades\Route;
Route::get('/{any}', function () {
    return view('welcome'); // This should be the name of your Blade file, like welcome.blade.php
})->where('any', '.*');