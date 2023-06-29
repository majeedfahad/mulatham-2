<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SettingController;
use \App\Http\Controllers\QuestionController;

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
})->middleware('guest');

Auth::routes();

Route::get('fakename', [\App\Http\Controllers\FakenameController::class, 'create'])->name('fakename.create');
Route::post('fakename', [\App\Http\Controllers\FakenameController::class, 'store'])->name('fakename.store');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/question/{id}', [App\Http\Controllers\HomeController::class, 'question'])->name('question');
Route::post('answerQuestion/{id}', [App\Http\Controllers\HomeController::class, 'answerQuestion'])->name('answerQuestion');


Route::middleware(['auth', 'settings'])->prefix('Settings')->name('settings.')->group(function() {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::get('/admin', [SettingController::class, 'admin'])->name('admin');
    Route::get('/users', [SettingController::class, 'users'])->name('users');
    Route::get('/deActiveSetting/{id}', [SettingController::class, 'deActiveSetting'])->name('deActiveSetting');
    Route::get('/activeSetting/{id}', [SettingController::class, 'activeSetting'])->name('activeSetting');

    Route::post('elimination', [SettingController::class, 'elimination'])->name('elimination');

    Route::prefix('Question')->name('questions.')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('index');
        Route::get('create', [QuestionController::class, 'create'])->name('create');
        Route::get('show/{id}', [QuestionController::class, 'show'])->name('show');
        Route::get('edit/{id}', [QuestionController::class, 'edit'])->name('edit');
        Route::post('store', [QuestionController::class, 'store'])->name('store');
        Route::post('update/{id}', [QuestionController::class, 'update'])->name('update');
        Route::get('destroy/{id}', [QuestionController::class, 'destroy'])->name('destroy');
//        Route::resource('/', QuestionController::class);
        Route::get('activeQuestion/{id}', [QuestionController::class, 'activeQuestion'])->name('activeQuestion');
        Route::get('deActiveQuestion/{id}', [QuestionController::class, 'deActiveQuestion'])->name('deActiveQuestion');
        Route::get('correctAnswer/{id}', [QuestionController::class, 'correctAnswer'])->name('correctAnswer');
        Route::get('wrongAnswer/{id}', [QuestionController::class, 'wrongAnswer'])->name('wrongAnswer');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
