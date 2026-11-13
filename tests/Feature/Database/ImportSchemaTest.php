<?php

namespace Tests\Feature\Database;

use App\Enums\DataQualityIssueStatus;
use App\Enums\DataQualityIssueType;
use App\Enums\IntegrationRunStatus;
use App\Models\AuditLog;
use App\Models\DataQualityIssue;
use App\Models\DataSource;
use App\Models\ExternalStockCurrent;
use App\Models\ExternalStockPosition;
use App\Models\IdempotencyKey;
use App\Models\IntegrationPartition;
use App\Models\IntegrationPayload;
use App\Models\IntegrationRun;
use App\Models\TransferOffer;
use Database\Seeders\DataSourceSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ImportSchemaTest extends TestCase
{
    use RefreshDatabase;

    private const TABLES = [
        'data_sources',
        'data_source_versions',
        'integration_runs',
        'integration_partitions',
        'integration_payloads',
        'external_stock_positions',
        'external_stock_current',
        'data_quality_issues',
        'idempotency_keys',
        'outbox_events',
        'audit_logs',
    ];

    public function test_migrations_can_be_rolled_back_and_reapplied()
    {
        foreach (self::TABLES as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabela {$table} deveria existir.");
        }

        Artisan::call('migrate:rollback', ['--step' => 5]);

        foreach (self::TABLES as $table) {
            $this->assertFalse(Schema::hasTable($table), "Tabela {$table} deveria ter sido removida.");
        }
        $this->assertTrue(Schema::hasTable('transfers'), 'O rollback não pode passar da parte 2.');

        Artisan::call('migrate');

        foreach (self::TABLES as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabela {$table} deveria ter voltado.");
        }
    }

    public function test_position_keeps_source_identifiers_and_separates_position_date_from_import_instant()
    {
        $position = ExternalStockPosition::factory()->create([
            'cnes' => '0000396',
            'external_medication_code' => 'BR0267891',
            'quantity' => '0.500',
            'position_date' => '2026-11-01',
            'imported_at' => '2026-11-13 03:00:00',
        ]);

        $fresh = $position->fresh();

        $this->assertNull($fresh?->facility_id, 'Sem unidade resolvida, o registro ainda é guardado.');
        $this->assertNull($fresh?->medication_id);
        $this->assertSame('0000396', $fresh?->cnes);
        $this->assertSame('BR0267891', $fresh?->external_medication_code);
        $this->assertSame('0.500', $fresh?->quantity);
        $this->assertSame('2026-11-01', $fresh?->position_date->toDateString());
        $this->assertSame('2026-11-13 03:00:00', $fresh?->imported_at->format('Y-m-d H:i:s'));
    }

    public function test_record_hash_is_unique_per_source()
    {
        $position = ExternalStockPosition::factory()->create(['record_hash' => str_repeat('a', 64)]);
        ExternalStockPosition::factory()->create(['record_hash' => str_repeat('a', 64)]);

        $this->assertDatabaseCount('external_stock_positions', 2);

        $this->expectException(QueryException::class);

        ExternalStockPosition::factory()->for($position->source, 'source')->create(['record_hash' => str_repeat('a', 64)]);
    }

    public function test_payload_is_unique_per_partition_page_and_content()
    {
        $payload = IntegrationPayload::factory()->create(['page_offset' => 3]);
        IntegrationPayload::factory()->for($payload->partition, 'partition')->create(['page_offset' => 3]);

        // Mesma página com conteúdo diferente é guardada de novo.
        $this->assertDatabaseCount('integration_payloads', 2);

        $this->expectException(QueryException::class);

        IntegrationPayload::factory()
            ->for($payload->partition, 'partition')
            ->create(['page_offset' => 3, 'content_hash' => $payload->content_hash]);
    }

    public function test_partition_key_is_unique_within_a_run()
    {
        $partition = IntegrationPartition::factory()->create(['partition_key' => 'uf=25']);

        $this->expectException(QueryException::class);

        IntegrationPartition::factory()->for($partition->run, 'run')->create(['partition_key' => 'uf=25']);
    }

    public function test_idempotency_key_is_unique_per_actor_and_operation()
    {
        IdempotencyKey::factory()->create(['actor_key' => 'user:1', 'operation' => 'reservations.store', 'idempotency_key' => 'k1']);
        IdempotencyKey::factory()->create(['actor_key' => 'user:2', 'operation' => 'reservations.store', 'idempotency_key' => 'k1']);
        IdempotencyKey::factory()->create(['actor_key' => 'user:1', 'operation' => 'transfers.collect', 'idempotency_key' => 'k1']);

        $this->assertDatabaseCount('idempotency_keys', 3);

        $this->expectException(QueryException::class);

        IdempotencyKey::factory()->create(['actor_key' => 'user:1', 'operation' => 'reservations.store', 'idempotency_key' => 'k1']);
    }

    public function test_current_projection_has_one_row_per_position_and_mirrors_it()
    {
        $current = ExternalStockCurrent::factory()->create();
        $position = $current->position;

        $this->assertSame($position->facility_id, $current->facility_id);
        $this->assertSame($position->medication_id, $current->medication_id);
        $this->assertSame($position->quantity, $current->quantity);
        $this->assertTrue($position->current?->is($current));

        $this->expectException(QueryException::class);

        ExternalStockCurrent::factory()->create(['external_stock_position_id' => $position->id]);
    }

    public function test_deleting_a_run_removes_partitions_and_payloads_but_keeps_positions()
    {
        $payload = IntegrationPayload::factory()->create();
        $position = ExternalStockPosition::factory()->create(['integration_payload_id' => $payload->id]);
        $issue = DataQualityIssue::factory()->create(['integration_run_id' => $payload->partition->integration_run_id]);

        $payload->partition->run->delete();

        $this->assertDatabaseCount('integration_partitions', 0);
        $this->assertDatabaseCount('integration_payloads', 0);
        $this->assertDatabaseHas('external_stock_positions', ['id' => $position->id, 'integration_payload_id' => null]);
        $this->assertDatabaseHas('data_quality_issues', ['id' => $issue->id, 'integration_run_id' => null]);
    }

    public function test_referenced_source_facility_and_medication_cannot_be_deleted()
    {
        $current = ExternalStockCurrent::factory()->create();

        foreach ([$current->source, $current->facility, $current->medication] as $referenced) {
            try {
                $referenced->delete();
                $this->fail(get_class($referenced).' referenciado pela projeção não deveria ser removido.');
            } catch (QueryException) {
                $this->assertTrue($referenced->fresh()?->exists);
            }
        }

        $run = IntegrationRun::factory()->create();

        $this->expectException(QueryException::class);

        $run->source->delete();
    }

    public function test_relationships_and_casts()
    {
        $run = IntegrationRun::factory()->create(['status' => IntegrationRunStatus::Running, 'scope' => ['codigo_uf' => '25', 'data' => '2026-11-01']]);
        $issue = DataQualityIssue::factory()->for($run, 'run')->create(['type' => DataQualityIssueType::ConflictingValues]);
        $offer = TransferOffer::factory()->create();
        $log = AuditLog::factory()->create([
            'auditable_type' => TransferOffer::class,
            'auditable_id' => $offer->id,
            'changes' => ['status' => ['draft', 'active']],
        ]);

        $this->assertSame(IntegrationRunStatus::Running, $run->fresh()?->status);
        $this->assertSame(['codigo_uf' => '25', 'data' => '2026-11-01'], $run->fresh()?->scope);
        $this->assertSame(DataQualityIssueType::ConflictingValues, $issue->fresh()?->type);
        $this->assertSame(DataQualityIssueStatus::Open, $issue->fresh()?->status);
        $this->assertTrue($run->issues()->first()?->is($issue));
        $this->assertTrue($log->auditable->is($offer));
        $this->assertSame(['status' => ['draft', 'active']], $log->fresh()?->changes);
    }

    public function test_data_source_seeder_is_idempotent()
    {
        $this->seed(DataSourceSeeder::class);
        $this->seed(DataSourceSeeder::class);

        $this->assertDatabaseCount('data_sources', 1);

        $source = DataSource::query()->where('slug', DataSourceSeeder::SLUG)->firstOrFail();
        $this->assertTrue($source->is_enabled);
        $this->assertSame('/daf/estoque-medicamentos-bnafar-horus', $source->settings['endpoint'] ?? null);
    }
}
