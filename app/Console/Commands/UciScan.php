<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UciService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UciScan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uci:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets users with active wifi vouchers and unblocks their ip addresses';

    /**
     * Execute the console command.
     */
    public function handle(UciService $uciService)
    {
        $active_ip_addresses = User::where('wifi_expires_at', '>', now())
            ->get(['ip_address'])
            ->pluck('ip_address');

        $cached_ip_addresses = Cache::get('ip_addresses');
        Cache::put('ip_addresses', $active_ip_addresses, 15);

        if ($cached_ip_addresses == $active_ip_addresses) {
            echo 'No changes detected, exiting...' . PHP_EOL;
            return 0;
        }

        echo 'Unblocking IP addresses: ' . implode(', ', array($active_ip_addresses));

        $result = $uciService->setUnblockedIpAddresses($active_ip_addresses);
        $result = $result === true ? 0 : 1;

        echo 'Result: ' . $result . PHP_EOL;

        return $result;
    }
}
