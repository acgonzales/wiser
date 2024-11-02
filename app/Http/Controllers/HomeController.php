<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $user = auth()->user();
        $device_ip = request()->header('X-Forwarded-For') ?? request()->ip();

        $show_countdown = false;
        if ($user->wifi_expires_at !== null && now()->isBefore($user->wifi_expires_at)) {
            $show_countdown = true;
        }

        return view('home', compact('user', 'device_ip', 'show_countdown'));
    }
}
