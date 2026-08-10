<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Klien ringan Binance USD-M Futures REST API.
 * Menandatangani request dengan HMAC-SHA256 (signed endpoints).
 *
 * Kredensial default diambil dari config('services.binance'),
 * atau bisa dioverride per-member lewat konstruktor.
 */
class BinanceService
{
    protected string $baseUrl;

    public function __construct(
        protected ?string $apiKey = null,
        protected ?string $apiSecret = null,
        ?string $baseUrl = null,
    ) {
        $this->apiKey = $apiKey ?: (string) config('services.binance.key');
        $this->apiSecret = $apiSecret ?: (string) config('services.binance.secret');
        $this->baseUrl = rtrim($baseUrl ?: (string) config('services.binance.base_url', 'https://fapi.binance.com'), '/');
    }

    public function isConfigured(): bool
    {
        return ! empty($this->apiKey) && ! empty($this->apiSecret);
    }

    /**
     * Uji koneksi + ambil saldo futures.
     *
     * @return array{ok: bool, message: string, balance?: float}
     */
    public function testConnection(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'message' => 'API key/secret belum diisi.'];
        }

        try {
            $data = $this->signedGet('/fapi/v2/account');

            if (isset($data['code'])) {
                return ['ok' => false, 'message' => $data['msg'] ?? 'Gagal terhubung.'];
            }

            return [
                'ok' => true,
                'message' => 'Terhubung ke Binance Futures.',
                'balance' => (float) ($data['totalWalletBalance'] ?? 0),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Error: '.$e->getMessage()];
        }
    }

    /**
     * Ambil riwayat realized PnL. Dikelompokkan per (symbol, tranId) menjadi
     * satu "trade" ringkas siap disimpan.
     *
     * @param  int|null  $startTime  epoch ms
     * @return array<int, array<string, mixed>>
     */
    public function fetchRealizedPnl(?int $startTime = null, ?int $endTime = null, int $limit = 1000): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $params = [
            'incomeType' => 'REALIZED_PNL',
            'limit' => $limit,
        ];
        if ($startTime) {
            $params['startTime'] = $startTime;
        }
        if ($endTime) {
            $params['endTime'] = $endTime;
        }

        $rows = $this->signedGet('/fapi/v1/income', $params);

        if (! is_array($rows) || isset($rows['code'])) {
            return [];
        }

        return collect($rows)
            ->filter(fn ($r) => (float) ($r['income'] ?? 0) != 0.0)
            ->map(fn ($r) => [
                'binance_id' => (string) ($r['tranId'] ?? ($r['tradeId'] ?? '')),
                'symbol' => $r['symbol'] ?? 'UNKNOWN',
                'pnl' => (float) $r['income'],
                'side' => (float) $r['income'] >= 0 ? 'long' : 'short',
                'closed_at' => isset($r['time']) ? (int) $r['time'] : null,
            ])
            ->values()
            ->all();
    }

    protected function signedGet(string $path, array $params = []): array
    {
        $params['timestamp'] = (int) (microtime(true) * 1000);
        $params['recvWindow'] = 10000;

        $query = http_build_query($params);
        $signature = hash_hmac('sha256', $query, $this->apiSecret);

        $response = Http::withHeaders(['X-MBX-APIKEY' => $this->apiKey])
            ->timeout(15)
            ->get("{$this->baseUrl}{$path}?{$query}&signature={$signature}");

        return $response->json() ?? [];
    }
}
