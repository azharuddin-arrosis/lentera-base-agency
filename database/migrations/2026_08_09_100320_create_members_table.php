<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('title')->nullable();           // jabatan / peran
            $table->string('tagline')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();           // path gambar/sketsa
            $table->string('specialization')->nullable();   // mis. Crypto Futures
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('location')->nullable();
            $table->date('joined_at')->nullable();
            $table->decimal('consultation_rate', 12, 2)->default(0); // tarif konsultasi per jam (Rupiah)

            // sosial media
            $table->string('telegram')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('tradingview')->nullable();
            $table->string('website')->nullable();

            // sinkron binance per-member (opsional)
            $table->string('binance_api_key')->nullable();
            $table->text('binance_api_secret')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
