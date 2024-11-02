<?php

namespace App\Services;

use App\Models\User;
use App\Models\WifiVoucher;

class VoucherService
{
    public static int $METAL_VALUE = 10;
    public static int $PAPER_VALUE = 8;
    public static int $OTHER_VALUE = 5;

    public function generateVoucher(int $value)
    {
        $code = str()->random(6);
        return WifiVoucher::create([
            'code' => $code,
            'value' => $value
        ]);
    }

    public function claimVoucher(User $user, string $code)
    {
        $voucher = WifiVoucher::where('code', $code)
            ->whereNull('user_id')
            ->firstOrFail();

        $now = now();
        $og_expires_at = $user->wifi_expires_at;

        if ($og_expires_at === null || $now->isAfter($og_expires_at)) {
            // User is probably a new user or previous session already expired
            // Therefore, creating new session with expiration relative to current time
            $expires_at = $now->addMinutes($voucher->value);
        } else {
            // User currently has an active session
            // Therefore, adding the new time to the current expiration
            $expires_at = $og_expires_at->addMinutes($voucher->value);
        }

        $user->wifi_expires_at = $expires_at;
        $voucher->user_id = $user->id;
        $voucher->claimed_at = $now;

        $user->save();
        $voucher->save();

        return $voucher;
    }
}
