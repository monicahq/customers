<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\DestroyAccount;
use App\Services\ValidateLicenceKey;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ValidationController extends Controller
{
    public function index(Request $request, string $key)
    {
        try {
            (new ValidateLicenceKey)->execute([
                'licence_key' => $key,
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'data' => '500',
            ], 500);
        } catch (Exception) {
            return response()->json([
                'data' => '501',
            ], 501);
        }

        return response()->json([
            'data' => 200,
        ], 200);
    }
}
