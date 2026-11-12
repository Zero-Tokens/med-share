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
        Schema::create('transfer_offers', function (Blueprint $table) {
            $table->id();
            // Unidade de origem: quem libera a quantidade.
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('medication_id')->constrained()->restrictOnDelete();
            $table->string('status', 20);
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            // Validade da confirmação da unidade, não do lote.
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['medication_id', 'status', 'expires_at']);
            $table->index(['facility_id', 'status']);
        });

        Schema::create('transfer_offer_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_offer_id')->constrained()->cascadeOnDelete();
            $table->string('lot_number', 50);
            $table->date('expires_on');
            // saldo ofertável = liberada - reservada - expedida (calculado, nunca gravado).
            $table->decimal('quantity_released', 14, 3);
            $table->decimal('quantity_reserved', 14, 3)->default(0);
            $table->decimal('quantity_shipped', 14, 3)->default(0);
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();

            $table->unique(['transfer_offer_id', 'lot_number']);
            $table->index('expires_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_offer_lots');
        Schema::dropIfExists('transfer_offers');
    }
};
