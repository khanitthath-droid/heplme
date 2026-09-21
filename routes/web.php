<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WantedBookController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MatchingController;
use App\Http\Controllers\ExchangeRequestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminBookController;
use App\Http\Controllers\AdminWantedBookController;
use App\Http\Controllers\AdminExchangeRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TradeRingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredBooks = \App\Models\Book::where('status', 'available')->with('user')->latest()->take(8)->get();
    $totalBooks = \App\Models\Book::where('status', 'available')->count();
    $totalUsers = \App\Models\User::count();
    $totalExchanges = \App\Models\ExchangeRequest::where('status', 'accepted')->count();

    return view('welcome', compact('featuredBooks', 'totalBooks', 'totalUsers', 'totalExchanges'));
});


Route::middleware('auth')->group(function () {

    // Dashboard ของ User
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');

    // Dashboard ของ Admin
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->middleware(['verified', 'admin'])
        ->name('admin.dashboard');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AdminUserController::class, 'index'])
            ->name('admin.users.index');

        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])
            ->name('admin.users.destroy');

        Route::get('/admin/books', [AdminBookController::class, 'index'])
            ->name('admin.books.index');

        Route::delete('/admin/books/{book}', [AdminBookController::class, 'destroy'])
            ->name('admin.books.destroy');

        Route::get('/admin/wanted-books', [AdminWantedBookController::class, 'index'])
            ->name('admin.wanted-books.index'); 

        Route::delete('/admin/wanted-books/{wantedBook}', [AdminWantedBookController::class, 'destroy'])
            ->name('admin.wanted-books.destroy');

        Route::get('/admin/exchange-requests', [AdminExchangeRequestController::class, 'index'])
            ->name('admin.exchange-requests.index');

        Route::delete('/admin/exchange-requests/{exchangeRequest}', [AdminExchangeRequestController::class, 'destroy'])
            ->name('admin.exchange-requests.destroy');

        // Reports Moderation
        Route::get('/admin/reports', [AdminReportController::class, 'index'])
            ->name('admin.reports.index');

        Route::patch('/admin/reports/{report}', [AdminReportController::class, 'update'])
            ->name('admin.reports.update');

        Route::delete('/admin/reports/messages/{chatMessage}', [AdminReportController::class, 'deleteMessage'])
            ->name('admin.reports.deleteMessage');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Books
    Route::get('/books', [BookController::class, 'index'])
        ->name('books.index');

    Route::get('/books/create', [BookController::class, 'create'])
        ->name('books.create');

    Route::post('/books', [BookController::class, 'store'])
        ->name('books.store');

    Route::get('/books/{book}', [BookController::class, 'show'])
        ->name('books.show');

    Route::get('/books/{book}/edit', [BookController::class, 'edit'])
        ->name('books.edit');

    Route::put('/books/{book}', [BookController::class, 'update'])
        ->name('books.update');

    Route::delete('/books/{book}', [BookController::class, 'destroy'])
        ->name('books.destroy');

    // Wanted Books
    Route::get('/wanted-books', [WantedBookController::class, 'index'])
        ->name('wanted-books.index');

    Route::get('/wanted-books/create', [WantedBookController::class, 'create'])
        ->name('wanted-books.create');

    Route::post('/wanted-books', [WantedBookController::class, 'store'])
        ->name('wanted-books.store');

    Route::delete('/wanted-books/{wantedBook}', [WantedBookController::class, 'destroy'])
        ->name('wanted-books.destroy');

    // Matching
    Route::get('/matching', [MatchingController::class, 'index'])
        ->name('matching.index');

    // Exchange Requests
    Route::get('/exchange-requests', [ExchangeRequestController::class, 'index'])
        ->name('exchange-requests.index');

    Route::post('/exchange-requests', [ExchangeRequestController::class, 'store'])
        ->name('exchange-requests.store');

    Route::match(['post', 'patch'], '/exchange-requests/{exchangeRequest}/accept', [ExchangeRequestController::class, 'accept'])
        ->name('exchange-requests.accept');

    Route::match(['post', 'patch'], '/exchange-requests/{exchangeRequest}/reject', [ExchangeRequestController::class, 'reject'])
        ->name('exchange-requests.reject');

    // Trade Rings (3-Way Circular Exchanges)
    Route::post('/trade-rings', [TradeRingController::class, 'store'])
        ->name('trade-rings.store');

    Route::match(['post', 'patch'], '/trade-rings/{tradeRing}/accept', [TradeRingController::class, 'accept'])
        ->name('trade-rings.accept');

    Route::match(['post', 'patch'], '/trade-rings/{tradeRing}/reject', [TradeRingController::class, 'reject'])
        ->name('trade-rings.reject');

    // Confirm Received Exchange
    Route::post('/exchange-requests/{exchangeRequest}/confirm-received', [ExchangeRequestController::class, 'confirmReceived'])
        ->name('exchange-requests.confirm-received');

    // In-App Chat
    Route::get('/chats', [ChatController::class, 'index'])
        ->name('chats.index');

    Route::get('/chats/{chatRoom}', [ChatController::class, 'index'])
        ->name('chats.show');

    Route::get('/exchange-requests/{exchangeRequest}/chat', [ChatController::class, 'start'])
        ->name('chats.start');

    Route::get('/chats/{chatRoom}/messages', [ChatController::class, 'fetchMessages'])
        ->name('chats.fetch');

    Route::post('/chats/{chatRoom}/messages', [ChatController::class, 'sendMessage'])
        ->name('chats.send');

    Route::post('/chats/{chatRoom}/structured', [ChatController::class, 'sendStructured'])
        ->name('chats.structured');

    Route::delete('/chats/messages/{chatMessage}', [ChatController::class, 'deleteMessage'])
        ->name('chats.deleteMessage');

    Route::post('/chats/{chatRoom}/typing', [ChatController::class, 'typing'])
        ->name('chats.typing');

    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])
        ->name('chats.unreadCount');

    // User Blocks
    Route::post('/user-blocks', [BlockController::class, 'store'])
        ->name('user-blocks.store');

    Route::delete('/user-blocks/{user}', [BlockController::class, 'destroy'])
        ->name('user-blocks.destroy');

    // Reports
    Route::post('/reports', [ReportController::class, 'store'])
        ->name('reports.store');
});

require __DIR__.'/auth.php';