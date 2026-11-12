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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            // Compromisso entre um item solicitado, um lote ofertado e uma transferência.
            $table->foreignId('transfer_id')->constrained()->restrictOnDelete();
            $table->foreignId('medication_request_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('transfer_offer_lot_id')->constrained()->restrictOnDelete();
            $table->string('status', 30);
            $table->decimal('quantity', 14, 3);
            $table->decimal('quantity_collected', 14, 3)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('reserved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['transfer_offer_lot_id', 'status']);
            $table->index(['medication_request_item_id', 'status']);
            $table->index('transfer_id');
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
