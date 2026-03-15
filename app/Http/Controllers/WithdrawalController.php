<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:10000'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:50'],
        ]);

        if ($user->balance < $validated['amount']) {
            throw ValidationException::withMessages([
                'amount' => 'Saldo Anda tidak mencukupi untuk penarikan ini.',
            ]);
        }

        DB::transaction(function () use ($user, $validated) {
            // Lock row for safety
            $lockedUser = \App\Models\User::query()->lockForUpdate()->find($user->id);

            if ($lockedUser->balance < $validated['amount']) {
                 throw ValidationException::withMessages([
                    'amount' => 'Saldo Anda tidak mencukupi.',
                ]);
            }

            Withdrawal::create([
                'user_id' => $lockedUser->id,
                'amount' => $validated['amount'],
                'bank_name' => $validated['bank_name'],
                'bank_account' => $validated['bank_account'],
                'status' => 'Pending',
            ]);

            $lockedUser->decrement('balance', $validated['amount']);
        });

        return back()->with('status', 'Permintaan penarikan saldo berhasil dikirim dan sedang menunggu review Admin.');
    }
}
