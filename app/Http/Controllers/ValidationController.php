<?php

namespace App\Http\Controllers;

use App\Services\ValidateLicenceKey;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function index(Request $request, string $secretKey, string $licenceKey)
    {
        if ($secretKey !== config('customers.customer_portal_secret_key')) {
            return response()->json([
                'data' => '404',
            ], 404);
        }

        try {
            (new ValidateLicenceKey)->execute([
                'licence_key' => $licenceKey,
            ]);
        } catch (ModelNotFoundException) {
            return response()->json([
                'data' => '404',
            ], 404);
        } catch (Exception) {
            return response()->json([
                'data' => '900',
            ], 900);
        }

        return response()->json([
            'data' => 200,
        ], 200);
    }
}
