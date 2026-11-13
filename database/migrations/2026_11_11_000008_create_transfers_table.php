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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            // Uma origem e um destino por transferência; várias origens geram várias transferências.
            $table->foreignId('origin_facility_id')->constrained('facilities')->restrictOnDelete();
            $table->foreignId('destination_facility_id')->constrained('facilities')->restrictOnDelete();
            $table->foreignId('medication_request_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status', 30);
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['origin_facility_id', 'status']);
            $table->index(['destination_facility_id', 'status']);
            $table->index('medication_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
