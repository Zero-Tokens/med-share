<?php

namespace Tests\Feature\Database;

use App\Enums\ExternalCodeSource;
use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Facility;
use App\Models\FacilityMembership;
use App\Models\Medication;
use App\Models\MedicationMapping;
use App\Models\Municipality;
use App\Models\MunicipalityMembership;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InitialSchemaTest extends TestCase
{
    use RefreshDatabase;

    private const TABLES = [
        'municipalities',
        'facilities',
        'medications',
        'medication_mappings',
        'municipality_memberships',
        'facility_memberships',
    ];

    public function test_migrations_can_be_rolled_back_and_reapplied()
    {
        foreach (self::TABLES as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabela {$table} deveria existir.");
        }
        $this->assertTrue(Schema::hasColumn('users', 'is_platform_admin'));

        // 15 passos: as migrations da operação (parte 2) e da importação (parte 3) dependem destas.
        Artisan::call('migrate:rollback', ['--step' => 15]);

        foreach (self::TABLES as $table) {
            $this->assertFalse(Schema::hasTable($table), "Tabela {$table} deveria ter sido removida.");
        }
        $this->assertFalse(Schema::hasColumn('users', 'is_platform_admin'));
        $this->assertTrue(Schema::hasTable('users'), 'O rollback não pode passar das migrations do starter kit.');

        Artisan::call('migrate');

        foreach (self::TABLES as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabela {$table} deveria ter voltado.");
        }
        $this->assertTrue(Schema::hasColumn('users', 'is_platform_admin'));
    }

    public function test_cnes_and_territorial_codes_preserve_leading_zeros()
    {
        $municipality = Municipality::factory()->create(['ibge_code' => '0012345', 'uf_code' => '01']);
        $facility = Facility::factory()->for($municipality)->create(['cnes' => '0000396']);

        $this->assertSame('0012345', $municipality->fresh()->ibge_code);
        $this->assertSame('01', $municipality->fresh()->uf_code);
        $this->assertSame('0000396', $facility->fresh()->cnes);
    }

    public function test_cnes_is_unique()
    {
        Facility::factory()->create(['cnes' => '0000396']);

        $this->expectException(QueryException::class);

        Facility::factory()->create(['cnes' => '0000396']);
    }

    public function test_external_code_is_unique_per_source()
    {
        MedicationMapping::factory()->create(['source' => ExternalCodeSource::Catmat, 'external_code' => 'BR0267891']);
        MedicationMapping::factory()->create(['source' => ExternalCodeSource::Obm, 'external_code' => 'BR0267891']);

        $this->assertDatabaseCount('medication_mappings', 2);

        $this->expectException(QueryException::class);

        MedicationMapping::factory()->create(['source' => ExternalCodeSource::Catmat, 'external_code' => 'BR0267891']);
    }

    public function test_user_has_at_most_one_membership_per_facility()
    {
        $membership = FacilityMembership::factory()->create();

        $this->expectException(QueryException::class);

        FacilityMembership::factory()
            ->for($membership->user)
            ->for($membership->facility)
            ->create(['role' => MembershipRole::Auditor]);
    }

    public function test_municipality_with_facilities_cannot_be_deleted()
    {
        $facility = Facility::factory()->create();

        $this->expectException(QueryException::class);

        $facility->municipality->delete();
    }

    public function test_deleting_a_user_removes_memberships_and_keeps_reviewed_mappings()
    {
        $user = User::factory()->create();
        FacilityMembership::factory()->for($user)->create();
        MunicipalityMembership::factory()->for($user)->create();
        $mapping = MedicationMapping::factory()->confirmed()->create(['reviewed_by' => $user->id]);

        $user->delete();

        $this->assertDatabaseCount('facility_memberships', 0);
        $this->assertDatabaseCount('municipality_memberships', 0);
        $this->assertDatabaseHas('medication_mappings', ['id' => $mapping->id, 'reviewed_by' => null]);
    }

    public function test_relationships_and_enum_casts()
    {
        $user = User::factory()->create();
        $facility = Facility::factory()->create();
        $membership = FacilityMembership::factory()
            ->for($user)
            ->for($facility)
            ->create(['role' => MembershipRole::LogisticsOperator]);
        MunicipalityMembership::factory()->for($user)->for($facility->municipality)->pending()->create();
        $medication = Medication::factory()->create(['quantity_precision' => 3]);
        $mapping = MedicationMapping::factory()->for($medication)->create();

        $this->assertTrue($user->facilityMemberships()->first()?->is($membership));
        $this->assertSame(MembershipRole::LogisticsOperator, $membership->fresh()?->role);
        $this->assertSame(MembershipStatus::Pending, $user->municipalityMemberships()->first()?->status);
        $this->assertTrue($facility->municipality->facilities()->first()?->is($facility));
        $this->assertTrue($medication->mappings()->first()?->is($mapping));
        $this->assertSame(ExternalCodeSource::Catmat, $mapping->fresh()?->source);
        $this->assertSame(3, $medication->fresh()?->quantity_precision);
        $this->assertFalse($user->fresh()?->is_platform_admin);
    }
}
