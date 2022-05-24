<?php

namespace App\Http\Controllers;

use App\Exceptions\PastDueLicenceException;
use App\Services\ValidateLicenceKey;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validation = app(ValidateLicenceKey::class)
                ->execute($request->only('licence_key'));

            return response()->json([
                'next_check_at' => $validation->format('Y-m-d'),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(status: 404);
        } catch (PastDueLicenceException) {
            return response()->json(status: 410);
        } catch (Exception) {
            return response()->json(status: 500);
        }
    }
}
