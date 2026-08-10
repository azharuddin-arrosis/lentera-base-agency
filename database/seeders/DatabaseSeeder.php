<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Member;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Admin ----
        User::query()->updateOrCreate(
            ['email' => 'admin@lentera.id'],
            ['name' => 'Admin Lentera', 'password' => bcrypt('password'), 'role' => 'admin'],
        );

        // ---- Founder (foto asli) ----
        $azhar = Member::updateOrCreate(['slug' => 'azharuddin-arrosis'], [
            'name' => 'Azharuddin Arrosis',
            'title' => 'Founder & Head of Trading',
            'tagline' => 'Strategi tanpa disiplin hanyalah harapan. Kami jalankan keduanya.',
            'bio' => 'Azharuddin adalah founder Lentera Project sekaligus kepala tim trading. '
                .'Berpengalaman lebih dari 8 tahun di pasar kripto & derivatif, ia menggabungkan analisis teknikal, '
                .'data on-chain, dan konteks makro untuk mengambil keputusan yang terukur. '
                .'Fokusnya membangun sistem trading yang konsisten dan mendidik anggota agar mandiri, bukan sekadar ikut sinyal.',
            'avatar' => '/images/team/azhar.jpg',
            'specialization' => 'Crypto Futures · Macro & On-chain',
            'experience_years' => 8,
            'location' => 'Surabaya, Indonesia',
            'joined_at' => '2018-01-10',
            'consultation_rate' => 500000,
            'telegram' => 'https://t.me/azharuddinarrosis',
            'instagram' => 'https://instagram.com/azharuddin.arrosis',
            'twitter' => 'https://x.com/azhararrosis',
            'tradingview' => 'https://tradingview.com/u/azharuddinarrosis',
            'website' => 'https://lentera.id',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 0,
        ]);
        $azhar->trades()->delete();
        $this->seedTrades($azhar, ['BTCUSDT', 'ETHUSDT', 'SOLUSDT', 'BNBUSDT'], 52, 0.68, 300);

        // ---- Member (avatar sketsa SVG) ----
        $andra = Member::updateOrCreate(['slug' => 'andra-pratama'], [
            'name' => 'Andra Pratama',
            'title' => 'Lead Consultant · Crypto Futures',
            'tagline' => 'Disiplin, sabar, dan selalu pakai manajemen risiko.',
            'bio' => 'Andra menekuni pasar kripto sejak 2018 dengan spesialisasi trading futures BTC & ETH. '
                .'Pendekatannya berbasis price action multi-timeframe dan manajemen risiko ketat (maks 1-2% per posisi). '
                .'Ia percaya konsistensi jauh lebih penting daripada sekadar profit besar sesekali.',
            'avatar' => '/images/team/andra.svg',
            'specialization' => 'Crypto Futures (BTC/ETH)',
            'experience_years' => 7,
            'location' => 'Surabaya, Indonesia',
            'joined_at' => '2019-03-01',
            'consultation_rate' => 350000,
            'telegram' => 'https://t.me/andrapratama',
            'instagram' => 'https://instagram.com/andra.trades',
            'twitter' => 'https://x.com/andratrades',
            'tradingview' => 'https://tradingview.com/u/andrapratama',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $bima = Member::updateOrCreate(['slug' => 'bima-saputra'], [
            'name' => 'Bima Saputra',
            'title' => 'Senior Analyst · Swing & Price Action',
            'tagline' => 'Baca struktur pasar, ikuti trend, kelola risiko.',
            'bio' => 'Bima adalah analis teknikal yang fokus pada swing trading dan price action. '
                .'Ia banyak membantu klien memahami struktur market, area supply-demand, serta psikologi trading. '
                .'Gaya tradingnya tenang dengan target realistis dan rasio risk-reward minimal 1:2.',
            'avatar' => '/images/team/bima.svg',
            'specialization' => 'Swing Trading · Price Action',
            'experience_years' => 5,
            'location' => 'Bandung, Indonesia',
            'joined_at' => '2021-06-15',
            'consultation_rate' => 300000,
            'telegram' => 'https://t.me/bimasaputra',
            'instagram' => 'https://instagram.com/bima.priceaction',
            'twitter' => 'https://x.com/bimapa',
            'tradingview' => 'https://tradingview.com/u/bimasaputra',
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $andra->trades()->delete();
        $bima->trades()->delete();

        $this->seedTrades($andra, ['BTCUSDT', 'ETHUSDT', 'SOLUSDT', 'BNBUSDT'], 42, 0.64, 220);
        $this->seedTrades($bima, ['ETHUSDT', 'SOLUSDT', 'XRPUSDT', 'AVAXUSDT', 'LINKUSDT'], 34, 0.60, 160);

        // ---- Akun login untuk Team (role: team) ----
        User::updateOrCreate(['email' => 'azharuddin@lentera.id'], [
            'name' => $azhar->name, 'password' => bcrypt('password'), 'role' => 'team', 'member_id' => $azhar->id,
        ]);
        User::updateOrCreate(['email' => 'andra@lentera.id'], [
            'name' => $andra->name, 'password' => bcrypt('password'), 'role' => 'team', 'member_id' => $andra->id,
        ]);

        // ---- 17 member tambahan (total 20), avatar inisial otomatis ----
        $names = [
            'Fajar Nugroho', 'Dimas Prasetyo', 'Rangga Wijaya', 'Yoga Saputra', 'Hendra Kusuma',
            'Reza Firmansyah', 'Aditya Ramadhan', 'Bagus Santoso', 'Galih Permana', 'Ilham Maulana',
            'Panca Wibowo', 'Surya Atmaja', 'Teguh Iman', 'Wahyu Setiawan', 'Zaki Abdullah',
            'Naufal Hakim', 'Rizky Pratama',
        ];
        $specs = ['Scalping BTC/ETH', 'Swing Trading', 'Altcoin Momentum', 'Price Action', 'Options & Hedging', 'Day Trading Index', 'On-chain Analysis', 'DeFi & Narrative'];
        $titles = ['Trader', 'Analis Teknikal', 'Konsultan Trading', 'Senior Trader', 'Trading Coach'];
        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Yogyakarta', 'Medan', 'Semarang'];
        $symbolsPool = [
            ['BTCUSDT', 'ETHUSDT'],
            ['SOLUSDT', 'BNBUSDT', 'ETHUSDT'],
            ['XRPUSDT', 'ADAUSDT', 'DOGEUSDT'],
            ['AVAXUSDT', 'LINKUSDT', 'DOTUSDT'],
            ['ARBUSDT', 'OPUSDT', 'SUIUSDT'],
        ];

        $order = 3;
        foreach ($names as $i => $name) {
            $m = Member::updateOrCreate(['slug' => \Illuminate\Support\Str::slug($name)], [
                'name' => $name,
                'title' => $titles[$i % count($titles)],
                'tagline' => 'Konsisten mengikuti sistem & manajemen risiko.',
                'bio' => $name.' adalah bagian dari tim konsultan Lentera Project. Fokus pada eksekusi disiplin, '
                    .'manajemen risiko yang ketat, dan edukasi anggota agar mampu trading secara mandiri.',
                'specialization' => $specs[$i % count($specs)],
                'experience_years' => 2 + ($i % 6),
                'location' => $cities[$i % count($cities)].', Indonesia',
                'joined_at' => now()->subMonths(6 + $i * 2)->format('Y-m-d'),
                'consultation_rate' => 200000 + ($i % 6) * 50000,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => $order++,
            ]);
            $m->trades()->delete();
            $this->seedTrades($m, $symbolsPool[$i % count($symbolsPool)], 20 + ($i % 15), 0.55 + ($i % 5) * 0.03, 120 + ($i % 5) * 40);
        }

        // ---- Layanan ----
        $services = [
            [
                'title' => 'Sinyal Trading Harian',
                'icon' => 'signal',
                'price_label' => 'Mulai Rp299rb / bln',
                'description' => 'Sinyal entry, target, dan stop-loss yang jelas langsung ke Telegram, lengkap dengan alasan analisis.',
                'features' => ['Sinyal harian BTC/ETH & altcoin pilihan', 'Entry, TP, SL jelas', 'Update kondisi market', 'Grup diskusi member'],
                'is_featured' => true, 'sort_order' => 1,
            ],
            [
                'title' => 'Mentoring 1-on-1',
                'icon' => 'graduation-cap',
                'price_label' => 'Rp1,5jt / paket',
                'description' => 'Bimbingan privat dari nol sampai bisa: dasar analisis, strategi, sampai psikologi & manajemen risiko.',
                'features' => ['6x sesi privat', 'Kurikulum bertahap', 'Review jurnal trading', 'Konsultasi via chat'],
                'is_featured' => true, 'sort_order' => 2,
            ],
            [
                'title' => 'Analisis Portofolio',
                'icon' => 'chart-pie',
                'price_label' => 'Rp500rb / sesi',
                'description' => 'Audit portofolio & gaya trading Anda, temukan kebocoran dan susun rencana perbaikan yang terukur.',
                'features' => ['Review posisi & histori', 'Evaluasi manajemen risiko', 'Rekomendasi perbaikan', 'Laporan tertulis'],
                'sort_order' => 3,
            ],
            [
                'title' => 'Managed Insight',
                'icon' => 'briefcase',
                'price_label' => 'Custom',
                'description' => 'Pendampingan intensif untuk klien serius: strategi disesuaikan dengan profil risiko & modal Anda.',
                'features' => ['Strategi personal', 'Laporan performa berkala', 'Prioritas konsultasi', 'Evaluasi bulanan'],
                'sort_order' => 4,
            ],
        ];
        foreach ($services as $s) {
            Service::updateOrCreate(['title' => $s['title']], $s + ['is_active' => true]);
        }

        // ---- Testimoni ----
        $testimonials = [
            ['author_name' => 'Rizal H.', 'author_role' => 'Member Sinyal · 8 bulan', 'rating' => 5, 'sort_order' => 1,
                'quote' => 'Sinyalnya jelas dan yang paling penting diajari manajemen risiko. Akun saya akhirnya konsisten hijau.'],
            ['author_name' => 'Dewi K.', 'author_role' => 'Alumni Mentoring', 'rating' => 5, 'sort_order' => 2,
                'quote' => 'Dari benar-benar nol sampai paham baca chart. Mentoringnya sabar dan ngga menggurui. Recommended!'],
            ['author_name' => 'Yoga P.', 'author_role' => 'Klien Analisis Portofolio', 'rating' => 5, 'sort_order' => 3,
                'quote' => 'Baru sadar banyak kebocoran di cara trading saya setelah diaudit tim Lentera. Worth it banget.'],
        ];
        foreach ($testimonials as $t) {
            Testimonial::updateOrCreate(['author_name' => $t['author_name']], $t + ['is_active' => true]);
        }

        // ---- Contoh lead ----
        Lead::updateOrCreate(['email' => 'calon.klien@example.com'], [
            'name' => 'Calon Klien', 'phone' => '0812-3456-7890',
            'subject' => 'Tanya paket mentoring', 'status' => 'new',
            'message' => 'Halo, saya tertarik dengan program mentoring 1-on-1. Bisa minta info jadwal dan detailnya?',
        ]);
    }

    /**
     * Generate trade realistis dengan win rate & modal tertentu (equity curve naik).
     */
    protected function seedTrades(Member $member, array $symbols, int $count, float $winRate, float $avgWin): void
    {
        mt_srand($member->id * 100);
        $start = Carbon::now()->subMonths(6);

        for ($i = 0; $i < $count; $i++) {
            $isWin = (mt_rand(1, 100) / 100) <= $winRate;
            $side = mt_rand(0, 1) ? 'long' : 'short';
            $symbol = $symbols[array_rand($symbols)];
            $leverage = [3, 5, 10, 10, 20][array_rand([3, 5, 10, 10, 20])];

            $magnitude = $avgWin * (0.3 + mt_rand(0, 170) / 100);
            $pnl = $isWin ? round($magnitude, 2) : round(-$magnitude * (0.35 + mt_rand(0, 60) / 100), 2);
            $roi = round($pnl / (200 + mt_rand(0, 400)) * 100, 2);

            $entry = $this->entryPrice($symbol);
            $closedAt = (clone $start)->addDays((int) ($i * (180 / $count)))->addHours(mt_rand(0, 20));

            Trade::create([
                'member_id' => $member->id,
                'symbol' => $symbol,
                'market' => 'futures',
                'side' => $side,
                'leverage' => $leverage,
                'entry_price' => $entry,
                'exit_price' => round($entry * (1 + ($pnl >= 0 ? 1 : -1) * (mt_rand(5, 60) / 1000)), 4),
                'quantity' => round(mt_rand(1, 50) / 10, 3),
                'pnl' => $pnl,
                'roi' => $roi,
                'fee' => round(abs($pnl) * 0.02, 2),
                'status' => 'closed',
                'source' => 'manual',
                'notes' => $isWin ? 'Setup sesuai plan, TP tercapai.' : 'Kena SL, market berbalik. Disiplin cut loss.',
                'opened_at' => (clone $closedAt)->subHours(mt_rand(1, 30)),
                'closed_at' => $closedAt,
            ]);
        }
    }

    protected function entryPrice(string $symbol): float
    {
        return match ($symbol) {
            'BTCUSDT' => mt_rand(58000, 72000),
            'ETHUSDT' => mt_rand(2900, 3900),
            'SOLUSDT' => mt_rand(130, 210),
            'BNBUSDT' => mt_rand(520, 680),
            'XRPUSDT' => mt_rand(50, 75) / 100,
            'AVAXUSDT' => mt_rand(25, 45),
            'LINKUSDT' => mt_rand(12, 22),
            default => mt_rand(1, 100),
        };
    }
}
