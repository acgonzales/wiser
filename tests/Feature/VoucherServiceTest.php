<?php

use App\Models\User;
use App\Models\WifiVoucher;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a user to claim a new voucher and updates their WiFi expiration time', function () {
    $service = new VoucherService;

    $user = User::factory()->create(['wifi_expires_at' => null]);
    $voucher = WifiVoucher::create(['code' => 'TESTVOUCHER', 'value' => 60]);

    $claimedVoucher = $service->claimVoucher($user, 'TESTVOUCHER');

    $user->refresh();
    expect($claimedVoucher->code)->toEqual($voucher->code);
    expect($user->wifi_expires_at)->not->toBeNull();
    expect($user->wifi_expires_at->setTime(0, 0))->toEqual(now()->addMinutes(60)->setTime(0, 0));
    expect($claimedVoucher->user_id)->toEqual($user->id);
});

it('allows a user to claim a new voucher after their time has expired', function () {
    $service = new VoucherService;

    $user = User::factory()->create(['wifi_expires_at' => now()->subMinutes(30)]);
    $voucher = WifiVoucher::create(['code' => 'TESTVOUCHER', 'value' => 60]);

    $claimedVoucher = $service->claimVoucher($user, 'TESTVOUCHER');

    $user->refresh();
    expect($claimedVoucher->code)->toEqual($voucher->code);
    expect($user->wifi_expires_at)->not->toBeNull();
    expect($user->wifi_expires_at->setTime(0, 0))->toEqual(now()->addMinutes(60)->setTime(0, 0));
    expect($claimedVoucher->user_id)->toEqual($user->id);
});

it('allows a user to claim an additional voucher and adds to their remaining time', function () {
    $service = new VoucherService;

    $user = User::factory()->create(['wifi_expires_at' => now()->addMinutes(30)]);
    $voucher = WifiVoucher::create(['code' => 'TESTVOUCHER', 'value' => 60]);

    $claimedVoucher = $service->claimVoucher($user, 'TESTVOUCHER');

    $user->refresh();
    expect($claimedVoucher->code)->toEqual($voucher->code);
    expect($user->wifi_expires_at)->not->toBeNull();
    expect($user->wifi_expires_at->setTime(0, 0))->toEqual(now()->addMinutes(90)->setTime(0, 0)); // Previous 30 + new 60
    expect($claimedVoucher->user_id)->toEqual($user->id);
});
