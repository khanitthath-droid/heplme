<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\WantedBook;
use App\Models\ExchangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // สถิติส่วนตัว (My Stats)
        $myBooksCount = Book::where('user_id', $user->id)->count();
        $myAvailableBooksCount = Book::where('user_id', $user->id)->where('status', 'available')->count();
        $myWantedBooksCount = WantedBook::where('user_id', $user->id)->count();
        $receivedPendingCount = ExchangeRequest::where('receiver_id', $user->id)->where('status', 'pending')->count();
        $sentPendingCount = ExchangeRequest::where('requester_id', $user->id)->where('status', 'pending')->count();
        $successfulExchangesCount = ExchangeRequest::where(function ($q) use ($user) {
            $q->where('requester_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })->where('status', 'accepted')->count();

        // สถิติภาพรวมระบบ (Platform Stats)
        $totalPlatformBooks = Book::where('status', 'available')->count();
        $totalPlatformUsers = User::count();
        $totalPlatformExchanges = ExchangeRequest::where('status', 'accepted')->count();

        // หาจำนวน Match สำหรับผู้ใช้คนนี้ (รวม 2-Way และ 3-Way Circular)
        $matchCounts = \App\Services\MatchingService::getMatchCounts($user);
        $matchesCount = $matchCounts['total'];
        $twoWayMatchesCount = $matchCounts['two_way'];
        $threeWayMatchesCount = $matchCounts['three_way'];

        // หนังสือใหม่ในชุมชนที่พร้อมให้แลกเปลี่ยน (ไม่รวมของตัวเอง)
        $communityBooks = Book::where('user_id', '!=', $user->id)
            ->where('status', 'available')
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        // คำขอล่าสุดที่เกี่ยวข้องกับผู้ใช้
        $recentRequests = ExchangeRequest::where(function ($q) use ($user) {
            $q->where('requester_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })
        ->with(['requester', 'receiver', 'offeredBook', 'requestedBook'])
        ->latest()
        ->take(4)
        ->get();

        return view('dashboard', compact(
            'myBooksCount',
            'myAvailableBooksCount',
            'myWantedBooksCount',
            'receivedPendingCount',
            'sentPendingCount',
            'successfulExchangesCount',
            'totalPlatformBooks',
            'totalPlatformUsers',
            'totalPlatformExchanges',
            'matchesCount',
            'twoWayMatchesCount',
            'threeWayMatchesCount',
            'communityBooks',
            'recentRequests'
        ));
    }
}
