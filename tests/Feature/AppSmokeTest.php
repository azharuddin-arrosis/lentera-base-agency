<?php

use App\Models\Booking;
use App\Models\Lead;
use App\Models\Member;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Trade;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->member = Member::create([
        'name' => 'Test Trader',
        'slug' => 'test-trader',
        'title' => 'Tester',
        'is_active' => true,
    ]);
    Trade::create([
        'member_id' => $this->member->id,
        'symbol' => 'BTCUSDT',
        'side' => 'long',
        'pnl' => 150,
        'roi' => 5,
        'status' => 'closed',
        'source' => 'manual',
        'closed_at' => now()->subDays(2),
    ]);
});

// ---- Publik ----
it('menampilkan halaman publik', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/', '/team', '/team/test-trader', '/performance', '/performance?member=test-trader', '/contact']);

it('menampilkan halaman jadwal & menyimpan booking multi-jam', function () {
    $this->get('/schedule')->assertOk();

    $date = now()->addWeek()->startOfWeek(); // Senin depan
    $this->post('/schedule', [
        'consultant' => 'admin',
        'name' => 'Sari', 'email' => 'sari@example.com',
        'date' => $date->format('Y-m-d'), 'times' => ['10:00', '11:00'],
    ])->assertRedirect();

    $booking = Booking::where('email', 'sari@example.com')->first();
    expect($booking)->not->toBeNull();
    expect($booking->hours)->toBe(2);
    expect((float) $booking->total_price)->toBe(2.0 * 150000);
});

it('menolak jam istirahat (12:00) dan hari Sabtu', function () {
    $monday = now()->addWeek()->startOfWeek();
    $this->post('/schedule', [
        'consultant' => 'admin', 'name' => 'A', 'email' => 'a@example.com',
        'date' => $monday->format('Y-m-d'), 'times' => ['12:00'],
    ])->assertSessionHasErrors('times');

    $saturday = now()->addWeek()->startOfWeek()->addDays(5);
    $this->post('/schedule', [
        'consultant' => 'admin', 'name' => 'A', 'email' => 'a@example.com',
        'date' => $saturday->format('Y-m-d'), 'times' => ['10:00'],
    ])->assertSessionHasErrors('times');
});

it('menolak booking pada slot yang sudah dipesan', function () {
    $date = now()->addWeek()->startOfWeek();
    Booking::create([
        'member_id' => null, 'consultant_name' => 'Tim Lentera (Admin)',
        'name' => 'A', 'email' => 'a@example.com',
        'date' => $date->format('Y-m-d'), 'times' => ['11:00'],
        'hours' => 1, 'rate' => 150000, 'total_price' => 150000, 'status' => 'confirmed',
    ]);

    $this->post('/schedule', [
        'consultant' => 'admin', 'name' => 'B', 'email' => 'b@example.com',
        'date' => $date->format('Y-m-d'), 'times' => ['11:00'],
    ])->assertSessionHasErrors('times');
});

it('menyimpan lead dari form kontak', function () {
    $this->post('/contact', [
        'name' => 'Budi', 'email' => 'budi@example.com', 'message' => 'Halo mau tanya',
    ])->assertRedirect();

    expect(Lead::where('email', 'budi@example.com')->exists())->toBeTrue();
});

// ---- Admin butuh login ----
it('menolak akses admin tanpa login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/admin/members')->assertRedirect('/login');
});

it('membuka halaman admin setelah login', function (string $url) {
    $this->actingAs($this->admin)->get($url)->assertOk();
})->with([
    '/dashboard',
    '/admin/members', '/admin/members/create',
    '/admin/trades', '/admin/trades/create',
    '/admin/services', '/admin/services/create',
    '/admin/testimonials', '/admin/testimonials/create',
    '/admin/leads', '/admin/binance', '/admin/bookings', '/admin/financial-plan',
]);

it('membuka form edit admin', function () {
    $service = Service::create(['title' => 'Layanan A', 'is_active' => true]);
    $testi = Testimonial::create(['author_name' => 'X', 'quote' => 'bagus', 'rating' => 5]);
    $trade = Trade::where('member_id', $this->member->id)->first();

    $this->actingAs($this->admin)->get("/admin/members/{$this->member->id}")->assertOk();
    $this->actingAs($this->admin)->get("/admin/members/{$this->member->id}/edit")->assertOk();
    $this->actingAs($this->admin)->get("/admin/trades/{$trade->id}/edit")->assertOk();
    $this->actingAs($this->admin)->get("/admin/services/{$service->id}/edit")->assertOk();
    $this->actingAs($this->admin)->get("/admin/testimonials/{$testi->id}/edit")->assertOk();
});

it('membuat & menghapus member via admin', function () {
    $this->actingAs($this->admin)->post('/admin/members', [
        'name' => 'Charlie', 'is_active' => true, 'is_featured' => false,
    ])->assertRedirect('/admin/members');

    $charlie = Member::where('name', 'Charlie')->first();
    expect($charlie)->not->toBeNull();
    expect($charlie->slug)->toBe('charlie');

    $this->actingAs($this->admin)->delete("/admin/members/{$charlie->id}")->assertRedirect();
    expect(Member::find($charlie->id))->toBeNull();
});

it('membatasi akses admin untuk role team & user', function () {
    $team = User::factory()->create(['role' => 'team', 'member_id' => $this->member->id]);
    $user = User::factory()->create(['role' => 'user']);

    // team diarahkan ke panel, user (klien) diarahkan ke situs publik
    $this->actingAs($team)->get('/admin/members')->assertRedirect('/panel');
    $this->actingAs($user)->get('/admin/members')->assertRedirect('/');
    $this->actingAs($user)->get('/dashboard')->assertRedirect('/');
    $this->actingAs($user)->get('/panel')->assertRedirect('/');
});

it('team bisa buka panel & kosongkan jadwal sendiri', function () {
    $team = User::factory()->create(['role' => 'team', 'member_id' => $this->member->id]);

    $this->actingAs($team)->get('/panel')->assertOk();

    $date = now()->addWeek()->startOfWeek()->format('Y-m-d');
    $this->actingAs($team)->post('/panel/blocks', [
        'date' => $date, 'full_day' => true, 'reason' => 'Cuti',
    ])->assertRedirect();

    expect(\App\Models\ScheduleBlock::where('member_id', $this->member->id)->exists())->toBeTrue();

    // admin diarahkan keluar dari panel
    $this->actingAs($this->admin)->get('/panel')->assertRedirect('/dashboard');
});

it('klien upload bukti bayar & beri ulasan lewat link status, admin verifikasi', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    $booking = \App\Models\Booking::create([
        'member_id' => $this->member->id, 'consultant_name' => $this->member->name,
        'name' => 'Klien', 'email' => 'klien@example.com',
        'date' => now()->subDay()->format('Y-m-d'), 'times' => ['10:00'],
        'hours' => 1, 'rate' => 300000, 'total_price' => 300000, 'status' => 'done',
    ]);

    // token otomatis dibuat
    expect($booking->token)->not->toBeNull();

    // halaman status booking terbuka tanpa login (publik via token)
    $this->get("/booking/{$booking->token}")->assertOk();

    // admin bisa buka detail
    $this->actingAs($this->admin)->get("/admin/bookings/{$booking->id}")->assertOk();

    // klien upload bukti → menunggu verifikasi (bukan langsung lunas)
    $this->post("/booking/{$booking->token}/proof", [
        'payment_proof' => \Illuminate\Http\UploadedFile::fake()->image('bukti.jpg'),
    ])->assertRedirect();
    expect($booking->fresh()->payment_status)->toBe('awaiting');
    expect($booking->fresh()->payment_proof)->not->toBeNull();

    // klien memberi ulasan (status sudah done)
    $this->post("/booking/{$booking->token}/review", [
        'rating' => 5, 'review' => 'Sangat membantu!',
    ])->assertRedirect();
    expect($booking->fresh()->rating)->toBe(5);

    // admin verifikasi pembayaran → lunas
    $this->actingAs($this->admin)->post("/admin/bookings/{$booking->id}/paid")->assertRedirect();
    expect($booking->fresh()->payment_status)->toBe('paid');
});

it('menolak ulasan bila konsultasi belum selesai', function () {
    $booking = \App\Models\Booking::create([
        'member_id' => $this->member->id, 'consultant_name' => $this->member->name,
        'name' => 'Klien', 'email' => 'k2@example.com',
        'date' => now()->addWeek()->format('Y-m-d'), 'times' => ['10:00'],
        'hours' => 1, 'rate' => 300000, 'total_price' => 300000, 'status' => 'confirmed',
    ]);

    $this->post("/booking/{$booking->token}/review", ['rating' => 5])
        ->assertSessionHasErrors('rating');
    expect($booking->fresh()->rating)->toBeNull();
});

it('profil publik menampilkan konsultasi sukses & rating', function () {
    \App\Models\Booking::create([
        'member_id' => $this->member->id, 'consultant_name' => $this->member->name,
        'name' => 'Klien', 'email' => 'k@example.com',
        'date' => now()->subDay()->format('Y-m-d'), 'times' => ['10:00'],
        'hours' => 1, 'rate' => 300000, 'total_price' => 300000, 'status' => 'done', 'rating' => 4, 'review' => 'Mantap',
    ]);

    $this->get('/team/test-trader')
        ->assertInertia(fn ($page) => $page
            ->where('consultation.done_count', 1)
            ->where('consultation.rating_count', 1)
            ->has('reviews', 1)
        );
});

it('admin membuat & menghapus signal', function () {
    $this->actingAs($this->admin)->get('/admin/signals')->assertOk();
    $this->actingAs($this->admin)->get('/admin/signals/create')->assertOk();

    $this->actingAs($this->admin)->post('/admin/signals', [
        'pair' => 'BTCUSDT', 'market' => 'futures', 'side' => 'long',
        'timeframe' => '4H', 'leverage' => '10x',
        'entry' => 62000, 'stop_loss' => 60500,
        'take_profits' => [63000, 64000, ''],
        'analysis' => 'Breakout struktur.',
        'status' => 'open', 'is_premium' => false,
        'published_at' => now()->toDateTimeString(),
    ])->assertRedirect('/admin/signals');

    $signal = \App\Models\Signal::where('pair', 'BTCUSDT')->first();
    expect($signal)->not->toBeNull();
    expect($signal->author_name)->toBe('Tim Lentera');
    expect($signal->take_profits)->toBe([63000, 64000]); // target kosong dibuang

    $this->actingAs($this->admin)->delete("/admin/signals/{$signal->id}")->assertRedirect();
    expect(\App\Models\Signal::find($signal->id))->toBeNull();
});

it('halaman publik signal mengunci detail premium untuk tamu', function () {
    // sinyal gratis (published)
    \App\Models\Signal::create([
        'pair' => 'ETHUSDT', 'market' => 'futures', 'side' => 'long',
        'entry' => 3000, 'stop_loss' => 2900, 'take_profits' => [3200],
        'status' => 'open', 'is_premium' => false, 'published_at' => now()->subHour(),
    ]);
    // sinyal premium (published)
    \App\Models\Signal::create([
        'pair' => 'SOLUSDT', 'market' => 'futures', 'side' => 'short',
        'entry' => 150, 'stop_loss' => 160, 'take_profits' => [140],
        'analysis' => 'rahasia', 'status' => 'open', 'is_premium' => true, 'published_at' => now()->subHour(),
    ]);
    // draft tidak boleh tampil
    \App\Models\Signal::create([
        'pair' => 'XRPUSDT', 'market' => 'spot', 'side' => 'long',
        'status' => 'open', 'is_premium' => false, 'published_at' => null,
    ]);

    // Tamu: hanya 2 sinyal published; premium terkunci (tanpa entry)
    $this->get('/signals')->assertInertia(fn ($page) => $page
        ->component('public/Signals')
        ->where('canSeePremium', false)
        ->has('signals.data', 2)
    );

    // Admin: premium terbuka
    $this->actingAs($this->admin)->get('/signals')->assertInertia(fn ($page) => $page
        ->where('canSeePremium', true)
    );
});

it('admin members: search & pagination server-side', function () {
    Member::create(['name' => 'Konsultan Unik XYZ', 'slug' => 'konsultan-unik-xyz', 'is_active' => true]);

    // tanpa filter → paginator berisi banyak (paling tidak 2: Test Trader + XYZ)
    $this->actingAs($this->admin)->get('/admin/members')
        ->assertInertia(fn ($page) => $page->has('members.data')->has('members.links'));

    // search mengerucutkan ke 1 hasil
    $this->actingAs($this->admin)->get('/admin/members?search=Unik+XYZ')
        ->assertInertia(fn ($page) => $page
            ->has('members.data', 1)
            ->where('members.data.0.name', 'Konsultan Unik XYZ')
            ->where('filters.search', 'Unik XYZ')
        );
});

it('detail signal publik & sitemap memuat data', function () {
    $pub = \App\Models\Signal::create([
        'pair' => 'BTCUSDT', 'market' => 'futures', 'side' => 'long',
        'entry' => 62000, 'stop_loss' => 60000, 'take_profits' => [64000],
        'status' => 'open', 'is_premium' => false, 'published_at' => now()->subHour(),
    ]);
    $draft = \App\Models\Signal::create([
        'pair' => 'ETHUSDT', 'market' => 'futures', 'side' => 'short',
        'status' => 'open', 'is_premium' => false, 'published_at' => null,
    ]);

    // detail signal published → 200, komponen benar
    $this->get("/signals/{$pub->id}")->assertOk()
        ->assertInertia(fn ($page) => $page->component('public/SignalDetail')->where('signal.pair', 'BTCUSDT'));

    // draft (belum publish) → 404
    $this->get("/signals/{$draft->id}")->assertNotFound();

    // sitemap memuat halaman signal + detail, tidak memuat /admin
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee(url('/signals'), false)
        ->assertSee(url('/signals/'.$pub->id), false)
        ->assertDontSee('/admin', false);

    // robots melarang area privat
    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /admin')->assertSee('Disallow: /booking');
});

it('SEO title unik per halaman (server-side)', function () {
    $this->get('/signals')->assertSee('<title>Signal Trading Crypto &amp; Futures — Lentera Project</title>', false);
    $this->get('/team/test-trader')->assertSee('Test Trader', false);
    // JSON-LD Organization ter-render (bukan Blade directive yang salah proses)
    $this->get('/')->assertSee('"@type":"Organization"', false)->assertSee('schema.org', false);
});

it('menginput PnL manual via admin', function () {
    $this->actingAs($this->admin)->post('/admin/trades', [
        'member_id' => $this->member->id,
        'symbol' => 'ETHUSDT', 'market' => 'futures', 'side' => 'short',
        'pnl' => -50, 'status' => 'closed', 'source' => 'manual',
        'closed_at' => now()->toDateTimeString(),
    ])->assertRedirect('/admin/trades');

    expect(Trade::where('symbol', 'ETHUSDT')->exists())->toBeTrue();
});

// ---- Financial Plan ----
it('menampilkan rekap financial plan dengan PnL harian & total WD', function () {
    \App\Models\Withdrawal::create([
        'member_id' => $this->member->id,
        'amount' => 500,
        'withdrawn_at' => now()->subDays(3)->toDateString(),
        'method' => 'USDT TRC20',
    ]);

    $this->actingAs($this->admin)->get('/admin/financial-plan')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/financial-plan/Index')
            ->where('summary.total_withdrawn', 500)
            ->where('summary.total_pnl', 150)
            ->where('summary.remaining', -350)
            ->has('daily', 1)
            ->has('withdrawals.data', 1));
});

it('mencatat & menghapus withdrawal via financial plan', function () {
    $this->actingAs($this->admin)->post('/admin/financial-plan/withdrawals', [
        'member_id' => $this->member->id,
        'amount' => 750,
        'withdrawn_at' => now()->toDateString(),
        'method' => 'Bank BCA',
    ])->assertRedirect('/admin/financial-plan');

    $wd = \App\Models\Withdrawal::firstWhere('amount', 750);
    expect($wd)->not->toBeNull();

    $this->actingAs($this->admin)->delete("/admin/financial-plan/withdrawals/{$wd->id}")->assertRedirect();
    expect(\App\Models\Withdrawal::find($wd->id))->toBeNull();
});

it('menolak withdrawal dengan jumlah kosong', function () {
    $this->actingAs($this->admin)->post('/admin/financial-plan/withdrawals', [
        'member_id' => $this->member->id,
        'withdrawn_at' => now()->toDateString(),
    ])->assertSessionHasErrors('amount');
});

it('memblokir financial plan untuk non-admin', function () {
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->get('/admin/financial-plan')->assertRedirect('/');
});
