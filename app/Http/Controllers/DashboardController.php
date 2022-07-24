<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Paddle\Receipt;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Laravel\Paddle\Receipt> */
        $receipts = Auth::user()->receipts;
        $receiptsCollection = $receipts->map(function (Receipt $receipt): array {
            return [
                'id' => $receipt->id,
                'amount' => $receipt->amount(),
                'currency' => $receipt->currency,
                'paid_at' => $receipt->paid_at->isoFormat('L'),
                'receipt_url' => $receipt->receipt_url,
            ];
        });

        return Inertia::render('Dashboard', [
            'receipts' => $receiptsCollection,
            'destroy_account' => route('current-user.destroy'),
        ]);
    }
}
