<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete(); // konsultan penulis; null = Tim Lentera
            $table->string('author_name')->default('Tim Lentera');
            $table->string('pair');                       // mis. BTCUSDT
            $table->string('market')->default('futures'); // spot / futures
            $table->string('side')->default('long');      // long / short
            $table->string('timeframe')->nullable();      // mis. 4H, 1D
            $table->string('leverage')->nullable();       // mis. 10x (khusus futures)
            $table->decimal('entry', 20, 8)->nullable();
            $table->decimal('entry_max', 20, 8)->nullable(); // opsional zona entry
            $table->decimal('stop_loss', 20, 8)->nullable();
            $table->json('take_profits')->nullable();     // array target [tp1, tp2, ...]
            $table->text('analysis')->nullable();         // alasan/analisa
            $table->string('status')->default('open');    // open / closed / cancelled
            $table->string('result')->nullable();         // running / tp_hit / sl_hit / breakeven
            $table->decimal('pnl_percent', 8, 2)->nullable();
            $table->boolean('is_premium')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};
