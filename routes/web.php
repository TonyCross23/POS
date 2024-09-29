<?php

use App\Livewire\Page\Cart;
use App\Livewire\Page\MyOrder;
use App\Livewire\Page\Product;
use App\Livewire\Page\Category;
use App\Livewire\Page\HomePage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\ForgotPage;
use App\Livewire\Page\CancelPage;
use App\Livewire\Page\SuccessPage;
use App\Livewire\Auth\RegisterPage;
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

Route::get('/login', LoginPage::class);
Route::get('register', RegisterPage::class);
Route::get('/forgot', ForgotPage::class);
Route::get('/reset-password', ResetPasswordPage::class);

Route::get('/', HomePage::class);
Route::get('/categories', Category::class);
Route::get('/products', Product::class);
Route::get('/products/{products}', ProductDetail::class);
Route::get('/cart', Cart::class);
Route::get('/my-order', MyOrder::class);
Route::get('/success', SuccessPage::class);
Route::get('/cancel', CancelPage::class);
