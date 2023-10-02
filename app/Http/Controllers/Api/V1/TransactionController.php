<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    //
    public function getTrasaction(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        $transactions = Transaction::with(['user.balance'])->paginate($perPage, ['*'], 'page', $page);

        $formattedData = $transactions->map(function ($transaction) {
            return [    
                'user_id' => $transaction->user->id,
                'user_name' => $transaction->user->name,
                'balance' => $transaction->user->balance->amount_available,
                'transaction' => [
                    'trx_id' => $transaction->trx_id,
                    'amount' => $transaction->amount,
                ],
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function postTrasaction(Request $request)
    {
        $inputAmount = $request->input('amount');
        $userId = auth()->user()->id;

        if ($inputAmount <= 0.00000001) {
            return response()->json(['message' => 'Penolakan: Jumlah tidak valid'], 400);
        }

        return DB::transaction(function () use ($userId, $inputAmount) {
            $balance = Balance::where('user_id', $userId)->lockForUpdate()->first();

            if (!$balance || $balance->amount_available < $inputAmount) {
                return response()->json(['message' => 'Penolakan: Saldo tidak mencukupi'], 400);
            }

            $transaction = Transaction::create([
                'user_id' => $userId,
                'amount' => $inputAmount,
            ]);

            sleep(30);

            $existingTransaction = Transaction::where('trx_id', $transaction->trx_id)->first();

            if ($existingTransaction) {
                return response()->json(['message' => 'Penolakan: Transaksi sudah ada'], 400);
            }

            $balance->amount_available -= $inputAmount;
            $balance->save();

            return response()->json([
                'trx_id' => $transaction->trx_id,
                'amount' => $transaction->amount,
            ], 200);
        });
    }
}
