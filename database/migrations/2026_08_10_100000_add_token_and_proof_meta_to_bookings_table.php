<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('token', 40)->nullable()->unique()->after('id');
            $table->timestamp('proof_uploaded_at')->nullable()->after('payment_proof');
        });

        // Backfill token untuk booking lama agar tiap booking punya link privat
        DB::table('bookings')->whereNull('token')->orderBy('id')->get(['id'])->each(function ($row) {
            DB::table('bookings')->where('id', $row->id)->update(['token' => (string) Str::random(40)]);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['token', 'proof_uploaded_at']);
        });
    }
};
