<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Book;
use App\Models\WantedBook;
use App\Models\ExchangeRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('users')) {
                    $userId = \Illuminate\Support\Facades\Auth::id();
                    $unreadChatCount = 0;

                    if ($userId && Schema::hasTable('chat_rooms') && Schema::hasTable('chat_messages')) {
                        $rooms = \App\Models\ChatRoom::where('user1_id', $userId)
                            ->orWhere('user2_id', $userId)
                            ->pluck('id');

                        $unreadChatCount = \App\Models\ChatMessage::whereIn('chat_room_id', $rooms)
                            ->where('sender_id', '!=', $userId)
                            ->where('is_read', false)
                            ->count();
                    }

                    $pendingReportsCount = 0;
                    if (Schema::hasTable('reports')) {
                        $pendingReportsCount = \App\Models\Report::where('status', 'pending')->count();
                    }

                    $view->with([
                        'navUsersCount' => User::count(),
                        'navBooksCount' => Book::count(),
                        'navWantedBooksCount' => WantedBook::count(),
                        'navExchangeRequestsCount' => ExchangeRequest::count(),
                        'navReportsCount' => $pendingReportsCount,
                        'navUnreadChatCount' => $unreadChatCount,
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore if database connection is not ready yet during setup
            }
        });
    }
}

