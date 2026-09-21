<?php

namespace App\Http\Controllers;

use App\Services\MatchingService;
use Illuminate\Support\Facades\Auth;

class MatchingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $twoWayMatches = MatchingService::getTwoWayMatches($user);
        $threeWayMatches = MatchingService::getThreeWayMatches($user);
        $oneWayMatches = MatchingService::getOneWayMatches($user);
        $counts = MatchingService::getMatchCounts($user);

        // $matches เพื่อรองรับโค้ดเก่าหรือส่งเสริมความเข้ากันได้
        $matches = $twoWayMatches;

        return view('matching.index', compact(
            'twoWayMatches',
            'threeWayMatches',
            'oneWayMatches',
            'counts',
            'matches'
        ));
    }
}