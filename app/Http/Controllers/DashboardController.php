<?php

namespace App\Http\Controllers;

use App\Services\DestroyAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $receipts = Auth::user()->receipts->map(function ($receipt) {
            return [
                'id' => $receipt->id,
                'amount' => $receipt->amount,
                'currency' => $receipt->currency,
                'paid_at' => $receipt->paid_at->format('Y-m-d'),
                'receipt_url' => $receipt->receipt_url,
            ];
        });

        return Inertia::render('Dashboard', [
            'receipts' =>  $receipts,
            'destroy_account' => route('dashboard.destroy'),
        ]);
    }

    public function destroy(Request $request)
    {
        $data = [
            'user_id' => Auth::user()->id,
        ];

        (new DestroyAccount)->execute($data);

        return response()->json([
            'data' => route('home'),
        ], 200);
    }
}
