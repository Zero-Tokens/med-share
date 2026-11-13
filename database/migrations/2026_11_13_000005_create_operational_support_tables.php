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
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            // Quem executou, como texto (ex.: user:12), para a chave valer mesmo sem usuário.
            $table->string('actor_key', 60);
            $table->string('operation', 60);
            $table->string('idempotency_key', 100);
            // Mesma chave com conteúdo diferente é conflito; igual devolve o resultado anterior.
            $table->string('content_hash', 64);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->json('response_body')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['actor_key', 'operation', 'idempotency_key']);
        });

        Schema::create('outbox_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 80);
            $table->string('aggregate_type', 60);
            $table->unsignedBigInteger('aggregate_id');
            $table->json('payload');
            $table->string('status', 20);
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('available_at');
            $table->timestamp('published_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['status', 'available_at']);
            $table->index(['aggregate_type', 'aggregate_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 80);
            $table->string('auditable_type', 80);
            $table->unsignedBigInteger('auditable_id');
            // Escopo da ação, quando houver.
            $table->foreignId('municipality_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('facility_id')->nullable()->constrained()->nullOnDelete();
            $table->text('justification')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'occurred_at']);
            $table->index('occurred_at');
            $table->index('facility_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('outbox_events');
        Schema::dropIfExists('idempotency_keys');
    }
};
