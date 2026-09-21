<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRequest;

class AdminExchangeRequestController extends Controller
{
    public function index()
    {
        $exchangeRequests = ExchangeRequest::with([
            'requester',
            'receiver',
            'offeredBook',
            'requestedBook'
        ])
        ->latest()
        ->get();

        return view(
            'admin.exchange-requests.index',
            compact('exchangeRequests')
        );
    }

    public function destroy(ExchangeRequest $exchangeRequest)
    {
        $exchangeRequest->delete();

        return redirect()
            ->route('admin.exchange-requests.index')
            ->with('success', 'ลบคำขอแลกเปลี่ยนเรียบร้อยแล้ว');
    }
}