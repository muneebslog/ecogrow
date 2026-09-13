<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scientific_name');
            $table->string('slug')->unique();
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('sunlight');
            $table->unsignedSmallInteger('water_frequency_days');
            $table->string('native_region')->nullable();
            $table->decimal('co2_offset_kg_per_year', 5, 2)->default(0);
            $table->string('care_difficulty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species');
    }
};
