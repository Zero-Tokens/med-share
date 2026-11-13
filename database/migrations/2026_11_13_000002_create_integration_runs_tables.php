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
        Schema::create('integration_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_source_id')->constrained()->restrictOnDelete();
            $table->foreignId('data_source_version_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 20);
            $table->string('status', 30);
            // Recorte da execução: UF, município, datas, programa.
            $table->json('scope')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('pages_processed')->default(0);
            $table->unsignedInteger('records_valid')->default(0);
            $table->unsignedInteger('records_rejected')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['data_source_id', 'status']);
            $table->index(['status', 'started_at']);
        });

        Schema::create('integration_partitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_run_id')->constrained()->cascadeOnDelete();
            // Ex.: uf=25;municipio=2507507;data=2026-11-01
            $table->string('partition_key', 120);
            $table->json('filters')->nullable();
            $table->string('status', 20);
            // Checkpoint: próxima página a buscar; só avança depois da persistência.
            $table->unsignedInteger('next_offset')->default(0);
            $table->unsignedInteger('pages_processed')->default(0);
            $table->unsignedInteger('records_valid')->default(0);
            $table->unsignedInteger('records_rejected')->default(0);
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['integration_run_id', 'partition_key']);
            $table->index(['status', 'integration_run_id']);
        });

        Schema::create('integration_payloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_partition_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('page_offset');
            $table->string('content_hash', 64);
            // Resposta original guardada no armazenamento privado.
            $table->string('storage_path');
            $table->unsignedInteger('record_count')->default(0);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->unique(['integration_partition_id', 'page_offset', 'content_hash'], 'integration_payloads_page_hash_unique');
            $table->index('content_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_payloads');
        Schema::dropIfExists('integration_partitions');
        Schema::dropIfExists('integration_runs');
    }
};
