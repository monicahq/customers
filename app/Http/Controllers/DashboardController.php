<?php

namespace App\Http\Controllers;

use App\Services\DestroyAccount;
use Illuminate\Http\Request;
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
                'amount' => $receipt->amount,
                'currency' => $receipt->currency,
                'paid_at' => $receipt->paid_at->format('Y-m-d'),
                'receipt_url' => $receipt->receipt_url,
            ];
        });

        return Inertia::render('Dashboard', [
            'receipts' => $receiptsCollection,
            'destroy_account' => route('dashboard.destroy'),
        ]);
    }

    public function destroy(Request $request)
    {
        $data = [
            'user_id' => Auth::id(),
        ];

        (new DestroyAccount)->execute($data);

        return response()->json([
            'data' => route('home'),
        ], 200);
    }
}
