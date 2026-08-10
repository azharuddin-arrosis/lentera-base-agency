<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Trade;
use App\Services\BinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BinanceController extends Controller
{
    public function index(): Response
    {
        $service = new BinanceService;

        return Inertia::render('admin/Binance', [
            'configured' => $service->isConfigured(),
            'members' => Member::orderBy('name')->get(['id', 'name']),
            'baseUrl' => config('services.binance.base_url'),
        ]);
    }

    public function test(): RedirectResponse
    {
        $result = (new BinanceService)->testConnection();

        $this->toast(
            $result['message'].(isset($result['balance']) ? ' Saldo: '.number_format($result['balance'], 2).' USDT' : ''),
            $result['ok'] ? 'success' : 'error',
        );

        return back();
    }

    public function sync(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'days' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $service = new BinanceService;
        if (! $service->isConfigured()) {
            $this->toast('API key/secret Binance belum diisi di .env.', 'error');

            return back();
        }

        $days = $data['days'] ?? 30;
        $start = now()->subDays($days)->getTimestampMs();
        $rows = $service->fetchRealizedPnl($start);

        $imported = 0;
        foreach ($rows as $row) {
            if (empty($row['binance_id'])) {
                continue;
            }

            $created = Trade::firstOrCreate(
                ['member_id' => $data['member_id'], 'binance_id' => $row['binance_id']],
                [
                    'symbol' => $row['symbol'],
                    'side' => $row['side'],
                    'market' => 'futures',
                    'pnl' => $row['pnl'],
                    'status' => 'closed',
                    'source' => 'binance',
                    'closed_at' => $row['closed_at'] ? now()->createFromTimestampMs($row['closed_at']) : now(),
                ]
            );

            if ($created->wasRecentlyCreated) {
                $imported++;
            }
        }

        $this->toast("Sinkron selesai. {$imported} trade baru diimpor dari Binance (".count($rows).' baris diproses).');

        return back();
    }
}
