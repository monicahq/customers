<?php

namespace App\Http\Controllers;

use App\Services\ValidateLicenceKey;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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
            return response()->json(status: 410);
        }

        return response()->json();
    }
}
