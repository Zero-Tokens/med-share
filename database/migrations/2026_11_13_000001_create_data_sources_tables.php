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
        Schema::create('data_sources', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name');
            $table->string('base_url');
            // Recurso de metadados monitorado para disparar importações (dados abertos).
            $table->string('resource_url')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('data_source_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_source_id')->constrained()->cascadeOnDelete();
            // Versão observada nos metadados da fonte (data de modificação, hash etc.).
            $table->string('version_key', 120);
            $table->timestamp('observed_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['data_source_id', 'version_key']);
            $table->index('observed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_source_versions');
        Schema::dropIfExists('data_sources');
    }
};
