<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('species_id')->constrained()->restrictOnDelete();
            $table->string('nickname')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('healthy');
            $table->timestamp('last_watered_at')->nullable();
            $table->string('photo_path')->nullable();
            $table->date('planted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
