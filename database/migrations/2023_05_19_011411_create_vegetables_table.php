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
        Schema::create('vegetables', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->string('name');
            $table->string('other_name')->nullable()->default(null);
            $table->string('thumbnail');
            $table->string('images');
            $table->text('description');
            $table->string('description_source');
            $table->text('how_to_plant');
            $table->string('how_to_plant_source');
            $table->text('plant_care');
            $table->string('plant_care_source');
            $table->text('plant_disease');
            $table->string('plant_disease_source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vegetables');
    }
};
