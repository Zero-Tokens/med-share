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
        Schema::create('medication_requests', function (Blueprint $table) {
            $table->id();
            // Unidade de destino: quem declara a falta.
            $table->foreignId('facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30);
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['facility_id', 'status']);
        });

        Schema::create('medication_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained()->restrictOnDelete();
            // Quantidades separadas para não atender a mesma necessidade duas vezes.
            $table->decimal('quantity_requested', 14, 3);
            $table->decimal('quantity_committed', 14, 3)->default(0);
            $table->decimal('quantity_shipped', 14, 3)->default(0);
            $table->decimal('quantity_received', 14, 3)->default(0);
            $table->string('urgency', 20);
            $table->date('needed_by')->nullable();
            $table->timestamps();

            $table->unique(['medication_request_id', 'medication_id']);
            $table->index(['medication_id', 'urgency', 'needed_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_request_items');
        Schema::dropIfExists('medication_requests');
    }
};
