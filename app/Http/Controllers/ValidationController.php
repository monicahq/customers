<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\ValidateLicenceKey;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ValidationController extends Controller
{
    public function index(Request $request, string $licenceKey)
    {
        try {
            (new ValidateLicenceKey)->execute([
                'licence_key' => $licenceKey,
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(status: 404);
        } catch (Exception) {
            return response()->json(status: 900);
        }

        return response()->json();
    }
}
