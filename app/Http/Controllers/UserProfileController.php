<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;

class UserProfileController extends Controller
{
    public function setBankInformation(Request $request)
    {
        BankAccount::create([
            'iban' => $request->iban,
            'user_id' => auth()->id()
        ]);
        return response()->json(['massage' => 'your information is saved'], 201);
    }
    public function getBankInformation()
    {
        return auth()->user()->bankAccounts;
    }
}
