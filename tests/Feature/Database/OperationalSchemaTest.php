<?php

namespace Tests\Feature\Database;

use App\Enums\ReservationStatus;
use App\Enums\TransferEventType;
use App\Enums\TransferStatus;
use App\Models\MedicationRequest;
use App\Models\MedicationRequestItem;
use App\Models\Reservation;
use App\Models\Transfer;
use App\Models\TransferApproval;
use App\Models\TransferDiscrepancy;
use App\Models\TransferEvent;
use App\Models\TransferItem;
use App\Models\TransferOffer;
use App\Models\TransferOfferLot;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OperationalSchemaTest extends TestCase
{
    use RefreshDatabase;

    private const TABLES = [
        'medication_requests',
        'medication_request_items',
        'transfer_offers',
        'transfer_offer_lots',
        'transfers',
        'reservations',
        'transfer_items',
        'transfer_approvals',
        'transfer_events',
        'transfer_discrepancies',
    ];

    public function test_migrations_can_be_rolled_back_and_reapplied()
    {
        foreach (self::TABLES as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabela {$table} deveria existir.");
        }

        // 10 passos: as 5 migrations da importação (parte 3) dependem destas.
        Artisan::call('migrate:rollback', ['--step' => 10]);

        foreach (self::TABLES as $table) {
            $this->assertFalse(Schema::hasTable($table), "Tabela {$table} deveria ter sido removida.");
        }
        $this->assertFalse(Schema::hasTable('external_stock_positions'));
        $this->assertTrue(Schema::hasTable('medications'), 'O rollback não pode passar da parte 1.');

        Artisan::call('migrate');

        foreach (self::TABLES as $table) {
            $this->assertTrue(Schema::hasTable($table), "Tabela {$table} deveria ter voltado.");
        }
    }

    public function test_quantities_are_stored_as_exact_decimals()
    {
        $lot = TransferOfferLot::factory()->create(['quantity_released' => '1234567890.125']);

        $this->assertSame('1234567890.125', $lot->fresh()?->quantity_released);
        $this->assertSame('0.000', $lot->fresh()?->quantity_reserved);
    }

    public function test_available_quantity_is_released_minus_reserved_minus_shipped()
    {
        $lot = TransferOfferLot::factory()->create([
            'quantity_released' => '200.000',
            'quantity_reserved' => '80.000',
        ]);
        $this->assertSame('120.000', $lot->availableQuantity());

        // Coleta dos 80: a reserva vira expedição e o saldo não volta.
        $lot->update(['quantity_reserved' => '0.000', 'quantity_shipped' => '80.000']);
        $this->assertSame('120.000', $lot->fresh()?->availableQuantity());

        $lot->update(['quantity_released' => '0.100', 'quantity_reserved' => '0.000', 'quantity_shipped' => '0.000']);
        $this->assertSame('0.100', $lot->fresh()?->availableQuantity());
    }

    public function test_a_request_lists_each_medication_once()
    {
        $item = MedicationRequestItem::factory()->create();

        $this->expectException(QueryException::class);

        MedicationRequestItem::factory()
            ->for($item->request, 'request')
            ->for($item->medication)
            ->create();
    }

    public function test_lot_number_is_unique_within_an_offer()
    {
        $lot = TransferOfferLot::factory()->create(['lot_number' => 'ABC123']);

        $this->expectException(QueryException::class);

        TransferOfferLot::factory()->for($lot->offer, 'offer')->create(['lot_number' => 'ABC123']);
    }

    public function test_a_reservation_has_at_most_one_transfer_item()
    {
        $item = TransferItem::factory()->create();

        $this->expectException(QueryException::class);

        TransferItem::factory()->for($item->reservation)->create();
    }

    public function test_transfer_item_is_coherent_with_its_reservation()
    {
        $item = TransferItem::factory()->create();
        $reservation = $item->reservation;

        $this->assertSame($reservation->transfer_id, $item->transfer_id);
        $this->assertSame($reservation->transfer_offer_lot_id, $item->transfer_offer_lot_id);
        $this->assertSame($reservation->requestItem?->medication_id, $item->medication_id);
        $this->assertSame($reservation->quantity, $item->quantity_planned);
        $this->assertTrue($reservation->transferItem?->is($item));
    }

    public function test_deleting_a_transfer_removes_its_records_but_not_reservations()
    {
        $transfer = Transfer::factory()->create();
        TransferApproval::factory()->for($transfer)->create();
        TransferEvent::factory()->for($transfer)->create();
        TransferDiscrepancy::factory()->for($transfer)->create();

        $transfer->delete();

        $this->assertDatabaseCount('transfer_approvals', 0);
        $this->assertDatabaseCount('transfer_events', 0);
        $this->assertDatabaseCount('transfer_discrepancies', 0);

        $reserved = Reservation::factory()->create();

        $this->expectException(QueryException::class);

        $reserved->transfer->delete();
    }

    public function test_referenced_facilities_and_medications_cannot_be_deleted()
    {
        $offer = TransferOffer::factory()->create();

        try {
            $offer->medication->delete();
            $this->fail('Medicamento com oferta não deveria ser removido.');
        } catch (QueryException) {
            $this->assertDatabaseHas('medications', ['id' => $offer->medication_id]);
        }

        $request = MedicationRequest::factory()->create();

        $this->expectException(QueryException::class);

        $request->facility->delete();
    }

    public function test_deleting_a_user_keeps_approvals_and_clears_other_references()
    {
        $reporter = User::factory()->create();
        $discrepancy = TransferDiscrepancy::factory()->create(['reported_by' => $reporter->id]);
        $reservation = Reservation::factory()->create(['reserved_by' => $reporter->id]);

        $reporter->delete();

        $this->assertDatabaseHas('transfer_discrepancies', ['id' => $discrepancy->id, 'reported_by' => null]);
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'reserved_by' => null]);

        $approval = TransferApproval::factory()->create();

        $this->expectException(QueryException::class);

        $approval->approver->delete();
    }

    public function test_relationships_and_enum_casts()
    {
        $transfer = Transfer::factory()->create(['status' => TransferStatus::InTransit]);
        $event = TransferEvent::factory()->for($transfer)->create([
            'type' => TransferEventType::Collected,
            'payload' => ['lot' => 'ABC123', 'quantity' => '80.000'],
        ]);
        $reservation = Reservation::factory()->for($transfer)->create(['status' => ReservationStatus::PartiallyCollected]);

        $this->assertSame(TransferStatus::InTransit, $transfer->fresh()?->status);
        $this->assertSame(TransferEventType::Collected, $event->fresh()?->type);
        $this->assertSame(['lot' => 'ABC123', 'quantity' => '80.000'], $event->fresh()?->payload);
        $this->assertSame(ReservationStatus::PartiallyCollected, $reservation->fresh()?->status);
        $this->assertTrue($transfer->events()->first()?->is($event));
        $this->assertTrue($transfer->reservations()->first()?->is($reservation));
        $this->assertTrue($transfer->origin->isNot($transfer->destination));
        $this->assertTrue($reservation->lot->offer->facility->exists);
        $this->assertTrue($reservation->requestItem?->request->facility->exists);
    }
}
