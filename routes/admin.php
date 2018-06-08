<?php

Route::get('/', function () {
    return view('admin.home');
})->name('home');
