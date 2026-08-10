<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete(); // null = admin/tim
            $table->date('date');
            $table->json('times')->nullable(); // null = seharian penuh
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_blocks');
    }
};
