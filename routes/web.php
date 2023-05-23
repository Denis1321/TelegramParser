<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramParser;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function (Request $request){
    return view('components.input-phone');
})->name('input-login');

Route::get('sendCode', function (Request $request){
    return view('components.input-code');
})->name('sendCode');

//Route::get('login', [TelegramParser::class, 'index'])->name('connect-telegram');
Route::post('sendPhone', [TelegramParser::class, 'store']);
Route::post('sendCode', [TelegramParser::class, 'store']);

Route::get('parse', [TelegramParser::class,'parseTiksanAuto']);
