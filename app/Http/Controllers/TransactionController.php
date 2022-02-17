<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Transaction;

use function PHPUnit\Framework\throwException;

class TransactionController extends Controller
{
    public function withdrawValidation(Request $request, $bankAccount)
    {
        $request->validate([
            'amount' => 'required|integer|min:1000',
            'account' => 'required|integer|exists:bank_accounts,id'
        ]);

        if (Auth::id() != $bankAccount->user_id) {
            return response()->json(['massage' => 'this is not your account'], 403);
        };

        if (Auth::user()->balance < $request->amount) {
            return response()->json(['massage' => 'insufficient balance'], 400);
        }
    }
    public function showTransactionList(BankAccount $bankAccount)
    {
        if (Auth::id() != $bankAccount->user_id) {
            return response()->json(['massage' => 'this is not your account'], 403);
        }
        return $bankAccount->transactions;
    }

    public function withdraw(Request $request)
    {
        $bankAccount = BankAccount::find($request->account);
        $this->withdrawValidation($request, $bankAccount);
        $client = env('FINNOTEC_CLIENT_ID', '0000');
        $token = env('FINNOTEC_BEARER_TOKEN', '0000');

        $trackId = (string) Str::uuid();
        try {
            $result = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $token"
            ])->post("https://devbeta.finnotech.ir/oak/v2/clients/$client/transferTo?trackId=${trackId}", [
                "amount" => $request->amount,
                "description" => 'withraw to ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
                "destinationFirstname" => Auth::user()->first_name,
                "destinationLastname" => Auth::user()->last_name,
                "destinationNumber" => $bankAccount->iban,
                "paymentNumber" => $trackId,
                "reasonDescription" => "withdraw",
            ])->json();
            if ($result['status'] != 'DONE') {
                throw new \Exception($result['error']['message']);
            }
            Transaction::create([
                "amount" => $request->amount,
                "description" => 'withraw to ' . Auth::user()->first_name . ' ' . Auth::user()->last_name,
                "reasonDescription" => "withdraw",
                "paymentNumber" => $trackId,
                'bank_account_id' => $bankAccount->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['massage' => 'transaction was not successfully'], 400);
        }
        return response()->json(['massage' => 'transaction was successfully'], 200);
    }
}
