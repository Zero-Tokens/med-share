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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipality_id')->constrained()->restrictOnDelete();
            // CNES como texto, para preservar zeros à esquerda.
            $table->string('cnes', 7)->unique();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('postal_code', 8)->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['municipality_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
