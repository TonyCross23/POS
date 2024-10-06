<?php

use App\Livewire\Page\Cart;
use App\Livewire\Page\MyOrder;
use App\Livewire\Page\HomePage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\ForgotPage;
use App\Livewire\Page\CancelPage;
use App\Livewire\Page\ProductPage;
use App\Livewire\Page\SuccessPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Page\CategoryPage;
use App\Livewire\Page\CheckOutPage;
use App\Livewire\Page\ProductDetail;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\ResetPasswordPage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', HomePage::class);
Route::get('/categories', CategoryPage::class);
Route::get('/products', ProductPage::class);
Route::get('/products/{slug}', ProductDetail::class);
Route::get('/cart', Cart::class);

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('register', RegisterPage::class);
    Route::get('/forgot', ForgotPage::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/logout', function () {
        auth()->logout();
        return redirect()->to('/');
    });
    Route::get('/check-out',CheckOutPage::class);
    Route::get('/my-order', MyOrder::class);
    Route::get('/success', SuccessPage::class);
    Route::get('/cancel', CancelPage::class);
});
