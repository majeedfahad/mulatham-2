<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

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

// =====================================================
// NEW GAME ROUTES (Session-based party game)
// =====================================================

// Landing page - create or join room
Route::get('/', [GameController::class, 'landing'])->name('game.landing');

// Room creation and joining
Route::post('/room/create', [GameController::class, 'createRoom'])->name('game.create');
Route::post('/room/join', [GameController::class, 'joinRoom'])->name('game.join');

// Lobby
Route::get('/room/{code}', [GameController::class, 'lobby'])->name('game.lobby');
Route::post('/room/{code}/ready', [GameController::class, 'toggleReady'])->name('game.ready');
Route::post('/room/{code}/start', [GameController::class, 'startGame'])->name('game.start');
Route::post('/room/{code}/leave', [GameController::class, 'leaveRoom'])->name('game.leave');

// Gameplay
Route::get('/room/{code}/play', [GameController::class, 'play'])->name('game.play');
Route::get('/room/{code}/state', [GameController::class, 'getState'])->name('game.state');

// Question Bank Phase
Route::post('/room/{code}/question-bank/acknowledge', [GameController::class, 'acknowledgeQuestionBank'])->name('game.questionBank.acknowledge');
Route::post('/room/{code}/question-bank/add', [GameController::class, 'submitQuestionToBank'])->name('game.questionBank.add');
Route::delete('/room/{code}/question-bank/{questionId}', [GameController::class, 'deleteQuestionFromBank'])->name('game.questionBank.delete');
Route::post('/room/{code}/question-bank/end', [GameController::class, 'endQuestionBankPhase'])->name('game.questionBank.end');

// Answering & Revealing
Route::post('/room/{code}/answer', [GameController::class, 'submitAnswer'])->name('game.answer');
Route::post('/room/{code}/start-reveal', [GameController::class, 'startReveal'])->name('game.startReveal');
Route::post('/room/{code}/cancel-reveal', [GameController::class, 'cancelReveal'])->name('game.cancelReveal');
Route::post('/room/{code}/reveal-timeout', [GameController::class, 'revealTimeout'])->name('game.revealTimeout');
Route::post('/room/{code}/reveal', [GameController::class, 'attemptReveal'])->name('game.reveal');
Route::post('/room/{code}/next', [GameController::class, 'nextQuestion'])->name('game.next');
Route::post('/room/{code}/end', [GameController::class, 'endGame'])->name('game.end');

// Results
Route::get('/room/{code}/results', [GameController::class, 'results'])->name('game.results');

// =====================================================
// SUGGESTIONS / FEEDBACK ROUTES
// =====================================================
use App\Http\Controllers\SuggestionController;

Route::prefix('suggestions')->name('suggestions.')->group(function () {
    Route::get('/', [SuggestionController::class, 'index'])->name('index');
    Route::get('/create', [SuggestionController::class, 'create'])->name('create');
    Route::post('/store', [SuggestionController::class, 'store'])->name('store');
    Route::post('/{suggestion}/vote', [SuggestionController::class, 'vote'])->name('vote');
    Route::post('/{suggestion}/unvote', [SuggestionController::class, 'unvote'])->name('unvote');
});

// Player status & management
Route::post('/room/{code}/heartbeat', [GameController::class, 'heartbeat'])->name('game.heartbeat');
Route::post('/room/{code}/kick/{playerId}', [GameController::class, 'kickPlayer'])->name('game.kick');

// =====================================================
// WEBHOOKS
// =====================================================
Route::prefix('webhooks')->name('webhooks.')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/sentry', [WebhookController::class, 'sentry'])->name('sentry');
});

// =====================================================
// LEGACY ROUTES (keeping for reference, can be removed)
// =====================================================

Route::get('/old', function () {
    return view('welcome');
})->middleware('guest')->name('old.welcome');

Auth::routes();

Route::get('fakename', [\App\Http\Controllers\FakenameController::class, 'create'])->name('fakename.create');
Route::post('fakename', [\App\Http\Controllers\FakenameController::class, 'store'])->name('fakename.store');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/question/{id}', [App\Http\Controllers\HomeController::class, 'question'])->name('question');
Route::post('answerQuestion/{id}', [App\Http\Controllers\HomeController::class, 'answerQuestion'])->name('answerQuestion');
Route::get('users', [App\Http\Controllers\HomeController::class, 'users'])->name('users');

Route::middleware(['auth', 'settings'])->prefix('Settings')->name('settings.')->group(function () {
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
        Route::get('activeQuestion/{id}', [QuestionController::class, 'activeQuestion'])->name('activeQuestion');
        Route::get('deActiveQuestion/{id}', [QuestionController::class, 'deActiveQuestion'])->name('deActiveQuestion');
        Route::get('correctAnswer/{id}', [QuestionController::class, 'correctAnswer'])->name('correctAnswer');
        Route::get('wrongAnswer/{id}', [QuestionController::class, 'wrongAnswer'])->name('wrongAnswer');
    });
});
