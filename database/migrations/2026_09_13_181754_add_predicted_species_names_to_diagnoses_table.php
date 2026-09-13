<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The identification most often names a real-world species that isn't
     * in our small internal catalog (predicted_species_id stays null in
     * that common case) — these columns store Plant.id's own name text
     * directly, so the result page always has something real to show.
     */
    public function up(): void
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->string('predicted_species_name')->nullable()->after('predicted_species_id');
            $table->string('predicted_scientific_name')->nullable()->after('predicted_species_name');
            $table->unsignedTinyInteger('is_plant_confidence')->nullable()->after('confidence');
        });
    }

    public function down(): void
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->dropColumn(['predicted_species_name', 'predicted_scientific_name', 'is_plant_confidence']);
        });
    }
};
