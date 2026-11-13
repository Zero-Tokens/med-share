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
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained()->cascadeOnDelete();
            // Execução física da reserva: previsto, coletado, recebido e aceito.
            $table->foreignId('reservation_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('medication_id')->constrained()->restrictOnDelete();
            $table->foreignId('transfer_offer_lot_id')->constrained()->restrictOnDelete();
            $table->decimal('quantity_planned', 14, 3);
            $table->decimal('quantity_collected', 14, 3)->default(0);
            $table->decimal('quantity_received', 14, 3)->default(0);
            $table->decimal('quantity_accepted', 14, 3)->default(0);
            $table->timestamps();

            $table->index('transfer_id');
            $table->index('medication_id');
            $table->index('transfer_offer_lot_id');
        });

        Schema::create('transfer_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained()->cascadeOnDelete();
            // Quem aprovou fica no registro: um usuário com aprovações não pode ser removido.
            $table->foreignId('approved_by')->constrained('users')->restrictOnDelete();
            $table->string('role', 30);
            $table->string('decision', 20);
            // Hash dos itens, lotes e quantidades aprovados; mudança material invalida a aprovação.
            $table->string('items_version', 64);
            $table->text('notes')->nullable();
            $table->timestamp('decided_at');
            $table->timestamps();

            $table->index(['transfer_id', 'decision']);
        });

        Schema::create('transfer_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->json('payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['transfer_id', 'occurred_at']);
        });

        Schema::create('transfer_discrepancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transfer_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->string('status', 20);
            $table->decimal('quantity_expected', 14, 3)->nullable();
            $table->decimal('quantity_found', 14, 3)->nullable();
            $table->text('description');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reconciled_at')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamps();

            $table->index(['transfer_id', 'status']);
            $table->index('transfer_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_discrepancies');
        Schema::dropIfExists('transfer_events');
        Schema::dropIfExists('transfer_approvals');
        Schema::dropIfExists('transfer_items');
    }
};
