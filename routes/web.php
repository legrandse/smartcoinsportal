<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/_admin', function () {
    return view('auth.login');
});

Route::get('/widget', function () {
    return view('admin.widget');
});

Route::get('/table', function () {
    return view('admin.table');
});

Route::get('/typo', function () {
    return view('admin.typography');
});

Route::get('/form', function () {
    return view('admin.form');
});

Route::get('/element', function () {
    return view('admin.element');
});

Route::get('/button', function () {
    return view('admin.button');
});

Route::get('/chart', function () {
    return view('admin.chart');
});

Route::get('/404', function () {
    return view('admin.404');
});

Auth::routes();

Route::get('/confirm-password', function () {
    return view('auth.confirm-password');
})->middleware('auth')->name('password.confirm');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $r) {
    $r->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/confirm-password', function (Request $request) {
    if (! Hash::check($request->password, $request->user()->password)) {
        return back()->withErrors([
            'password' => ['The provided password does not match our records.']
        ]);
    }
 
    $request->session()->passwordConfirmed();
 
    return redirect()->intended();
})->middleware(['auth', 'throttle:6,1']);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/distributors/settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('settings');
