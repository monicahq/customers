<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Vault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Vault\ManageVault\CreateVault;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Vault\ViewHelpers\VaultCreateViewHelper;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchase');
    }
}
