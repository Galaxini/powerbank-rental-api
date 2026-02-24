<?php

declare(strict_types=1);

namespace App\Domain\Rental\Services;

use App\Domain\PowerBank\Enums\PowerBankStatus;
use App\Domain\Rental\Enums\RentalStatus;
use App\Models\PowerBank;
use App\Models\Rental;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Support\Facades\DB;

class ReturnRentalService
{
    /**
     * @return array{rental: Rental, overdue_minutes: int, penalty_cents: int}
     */
    public function handle(Rental $rental, int $userId): array
    {
        return DB::transaction(function () use ($rental, $userId): array {
            $lockedRental = Rental::query()
                ->whereKey($rental->id)
                ->lockForUpdate()
                ->first();

            if (! $lockedRental) {
                throw new DomainException('Rental not found.', 404);
            }

            if ((int) $lockedRental->user_id !== $userId) {
                throw new DomainException('Rental does not belong to the authenticated user.', 403);
            }

            if (! in_array($lockedRental->status, [RentalStatus::ACTIVE, RentalStatus::OVERDUE], true)) {
                throw new DomainException('Rental can be returned only from ACTIVE or OVERDUE status.', 409);
            }

            $now = CarbonImmutable::now();
            $dueAt = CarbonImmutable::parse($lockedRental->due_at);
            $overdueMinutes = $now->greaterThan($dueAt) ? $dueAt->diffInMinutes($now) : 0;
            $penaltyCents = max(0, $overdueMinutes) * 100;

            $lockedRental->status = RentalStatus::RETURNED;
            $lockedRental->returned_at = $now;
            $lockedRental->penalty_cents = $penaltyCents;
            $lockedRental->save();

            $powerBank = PowerBank::query()
                ->whereKey($lockedRental->power_bank_id)
                ->lockForUpdate()
                ->first();

            if (! $powerBank) {
                throw new DomainException('Power bank not found for rental.', 409);
            }

            $powerBank->status = PowerBankStatus::AVAILABLE;
            $powerBank->save();

            return [
                'rental' => $lockedRental->fresh(),
                'overdue_minutes' => $overdueMinutes,
                'penalty_cents' => $penaltyCents,
            ];
        });
    }
}
