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
        Schema::create('external_stock_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_source_id')->constrained()->restrictOnDelete();
            $table->foreignId('integration_payload_id')->nullable()->constrained()->nullOnDelete();
            // Resolvidos pela normalização; nulos quando o CNES ou o código não foram reconhecidos.
            $table->foreignId('facility_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('medication_id')->nullable()->constrained()->restrictOnDelete();
            // Dimensões originais da fonte, preservadas como texto.
            $table->string('cnes', 7);
            $table->string('external_medication_code', 30);
            $table->string('external_medication_description')->nullable();
            $table->string('lot_number', 50)->nullable();
            $table->date('expires_on')->nullable();
            $table->decimal('quantity', 14, 3);
            $table->string('program_code', 20)->nullable();
            $table->string('origin_system', 20)->nullable();
            $table->string('product_type', 30)->nullable();
            // Data da posição (fonte) e instante da importação (nosso) são coisas diferentes.
            $table->date('position_date');
            $table->timestamp('imported_at');
            // Hash da chave composta: fonte, CNES, produto, lote, validade, programa, sistema, data.
            $table->string('record_hash', 64);
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['data_source_id', 'record_hash']);
            $table->index(['facility_id', 'medication_id', 'position_date'], 'external_stock_positions_facility_medication_date_index');
            $table->index(['cnes', 'position_date']);
            $table->index('position_date');
            $table->index('medication_id');
            $table->index('integration_payload_id');
        });

        Schema::create('external_stock_current', function (Blueprint $table) {
            $table->id();
            // Projeção consultável: uma linha por posição válida mais recente.
            $table->foreignId('external_stock_position_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('data_source_id')->constrained()->restrictOnDelete();
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('medication_id')->constrained()->restrictOnDelete();
            $table->string('lot_number', 50)->nullable();
            $table->date('expires_on')->nullable();
            $table->decimal('quantity', 14, 3);
            $table->date('position_date');
            $table->timestamp('imported_at');
            $table->string('quality_status', 20);
            $table->timestamps();

            $table->index(['facility_id', 'medication_id', 'quality_status'], 'external_stock_current_facility_medication_quality_index');
            $table->index(['medication_id', 'quality_status', 'position_date'], 'external_stock_current_medication_quality_date_index');
            $table->index('data_source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_stock_current');
        Schema::dropIfExists('external_stock_positions');
    }
};
