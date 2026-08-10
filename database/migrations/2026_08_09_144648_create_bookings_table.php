<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete(); // konsultan; null = admin/tim
            $table->string('consultant_name');    // snapshot nama konsultan
            $table->string('name');                // klien
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('date');
            $table->json('times');                 // array jam ["09:00","10:00"]
            $table->unsignedInteger('hours')->default(1);
            $table->decimal('rate', 12, 2)->default(0);        // tarif per jam
            $table->decimal('total_price', 14, 2)->default(0); // rate x hours
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending / confirmed / done / cancelled
            $table->timestamps();

            $table->index(['date']);
            $table->index(['member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
