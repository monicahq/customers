<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReceiptResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display Dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $receipts = $request->user()->receipts()->paginate(20);

        return Inertia::render('Dashboard', [
            'receipts' => ReceiptResource::collection($receipts),
        ]);
    }
}
