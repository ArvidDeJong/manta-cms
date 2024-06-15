<?php


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Manta\Staff\StaffLogin;
use App\Livewire\Manta\Staff\StaffPasswordForgot;
use App\Livewire\Manta\Staff\StaffPasswordReset;

// ! Prevent register
Route::get('/register', function () {
    return abort(404);
});

Route::get('/login', StaffLogin::class)->name('login');

Route::get('/staff/login', StaffLogin::class)->name('staff.login');
Route::get('/staff/wachtwoord-vergeten', StaffPasswordForgot::class)
    ->name('staff.password.request');
Route::get('/staff/wachtwoord-reset/{token}', StaffPasswordReset::class)
    ->name('staff.password.reset');

// Logout
Route::get('/logout', function () {
    if (Auth::user()) {
        Auth::logout();
        Auth::guard('web')->logout();
        Auth::guard('staff')->logout();
        sleep(1);
    }

    return redirect(url('/'));
})->name('users.logout');



// Clear settings
Route::get('/clearDgP', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('storage:link', []);
});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth:staff']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
