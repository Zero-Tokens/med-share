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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('active_ingredient')->nullable();
            $table->string('strength')->nullable();
            $table->string('pharmaceutical_form')->nullable();
            $table->string('presentation')->nullable();
            $table->string('supply_unit');
            // Casas decimais permitidas nas quantidades desta unidade de fornecimento.
            $table->unsignedTinyInteger('quantity_precision')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Sem unicidade por nome: a identidade vem dos códigos externos mapeados.
            $table->index('name');
            $table->index('active_ingredient');
        });

        Schema::create('medication_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->string('source', 20);
            $table->string('external_code', 30);
            $table->string('external_description')->nullable();
            $table->string('status', 20);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['source', 'external_code']);
            $table->index(['medication_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_mappings');
        Schema::dropIfExists('medications');
    }
};
