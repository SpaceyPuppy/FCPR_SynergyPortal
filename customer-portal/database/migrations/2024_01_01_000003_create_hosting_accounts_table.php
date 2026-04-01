<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosting_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('domain');
            $table->string('plan');
            $table->string('cpanel_username')->unique();
            $table->string('cpanel_url')->nullable();
            $table->string('server')->nullable();
            $table->enum('status', ['active', 'suspended', 'terminated', 'pending'])->default('active');
            $table->string('synergy_ref')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosting_accounts');
    }
};
