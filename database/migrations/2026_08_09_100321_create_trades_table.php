<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('symbol');                       // BTCUSDT
            $table->string('market')->default('futures');   // futures / spot
            $table->string('side')->default('long');        // long / short
            $table->decimal('leverage', 6, 2)->nullable();
            $table->decimal('entry_price', 20, 8)->nullable();
            $table->decimal('exit_price', 20, 8)->nullable();
            $table->decimal('quantity', 20, 8)->nullable();
            $table->decimal('pnl', 20, 8)->default(0);      // realized PnL (USDT)
            $table->decimal('roi', 10, 4)->nullable();      // persen
            $table->decimal('fee', 20, 8)->default(0);
            $table->string('status')->default('closed');    // open / closed
            $table->string('source')->default('manual');    // manual / binance
            $table->string('binance_id')->nullable();       // anti-duplikat sinkron
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'binance_id']);
            $table->index(['member_id', 'closed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
