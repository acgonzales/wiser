<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use function Illuminate\Log\log;

class UciService
{
    private string $host;
    private string $user;
    private string $password;
    private string $unblockConfig;
    private string $uci_url;

    public function __construct()
    {
        $this->host = env('UCI_HOST', 'http://192.168.8.1');
        $this->user = env('UCI_USER', 'root');
        $this->password = env('UCI_PASSWORD', 'wiser');
        $this->unblockConfig = env('UCI_UNBLOCK_CONFIG', 'cfg1892bd');
        $this->uci_url = $this->host . '/cgi-bin/luci/rpc';
    }

    public function setUnblockedIpAddresses(array | Collection $ip)
    {
        $ip = collect($ip);
        $payload = json_encode([
            'id' => 1,
            'method' => 'set',
            'params' => [
                'firewall',
                $this->unblockConfig,
                'src_ip',
                $ip->isNotEmpty() ? $ip : collect(["1.1.1.1"]),
            ],
        ]);

        $url = $this->uci_url . '/uci';
        $authToken = $this->getAuthToken();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBody($payload)->withQueryParameters(['auth' => $authToken])->get($url);

        $result = json_decode($response->body())->result;
        if ($result === true) {
            $this->applyChanges();
        }
        return $result;
    }

    public function getUnblockConfig(): mixed
    {
        $payload = json_encode([
            'id' => 1,
            'method' => 'get_all',
            'params' => [
                'firewall',
                $this->unblockConfig,
            ],
        ]);

        $url = $this->uci_url . '/uci';
        $authToken = $this->getAuthToken();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBody($payload)->withQueryParameters(['auth' => $authToken])->get($url);

        return json_decode($response->body())->result;
    }

    private function applyChanges()
    {
        $payload = json_encode([
            'id' => 1,
            'method' => 'apply'
        ]);

        $url = $this->uci_url . '/uci';
        $authToken = $this->getAuthToken();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBody($payload)->withQueryParameters(['auth' => $authToken])->get($url);
    }

    private function getAuthToken(): string
    {
        $cached_token = Cache::get('uci_token');
        if ($cached_token) {
            return $cached_token;
        }

        $payload = json_encode([
            'id' => 1,
            'method' => 'login',
            'params' => [
                $this->user,
                $this->password,
            ],
        ]);

        $url = $this->uci_url . '/auth';

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBody($payload)->get($url);

        $jsonResponse = json_decode($response->body());
        $token = $jsonResponse->result;

        Cache::put('uci_token', $token, 30);
        return $token;
    }
}
