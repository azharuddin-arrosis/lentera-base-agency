<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 20, 8);              // nominal ditarik (USD/USDT)
            $table->date('withdrawn_at');                  // tanggal penarikan
            $table->string('method')->nullable();          // mis. "USDT TRC20", "Bank BCA"
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'withdrawn_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
