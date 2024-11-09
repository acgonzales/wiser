<?php

namespace App\Http\Controllers;

use App\Services\VoucherService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function store(Request $request, VoucherService $voucherService)
    {
        $validated = $request->validate(['type' => 'required|string|in:metal,plastic,water,other']);

        if ($validated['type'] === 'metal') {
            $value = VoucherService::$METAL_VALUE;
        } elseif ($validated['type'] === 'plastic') {
            $value = VoucherService::$PLASTIC_VALUE;
        } elseif ($validated['type'] === 'water') {
            $value = VoucherService::$WATER_VALUE;
        } else {
            $value = VoucherService::$OTHER_VALUE;
        }

        $voucher = $voucherService->generateVoucher($value);
        return response()->json([
            'id' => $voucher->id,
            'code' => $voucher->code,
            'value' => $voucher->value,
        ]);
    }

    public function claim(Request $request, VoucherService $voucherService)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'ip_address' => 'required|ipv4',
        ]);

        try {
            $user = $request->user();
            $user->ip_address = $validated['ip_address'];
            $user->save();
            $user->refresh();

            $voucherService->claimVoucher($user, $validated['code']);
            session()->flash('message', 'Voucher claimed successfully.');
            return back();
        } catch (\Throwable $e) {
            return back()->withErrors([
                'code' => 'Invalid voucher code.',
            ]);
        }
    }
}
