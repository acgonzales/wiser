<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wifi_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('value', unsigned: true);
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
            $table->timestamp('wifi_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wifi_vouchers');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ip_address');
            $table->dropColumn('wifi_expires_at');
        });
    }
};
