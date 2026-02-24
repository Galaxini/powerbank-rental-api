<?php

declare(strict_types=1);

namespace App\Services\Rentals;

use App\DTO\Rentals\StartRentalData;
use App\Domain\PowerBank\Enums\PowerBankStatus;
use App\Domain\Rental\Enums\RentalStatus;
use App\Domain\Rental\Exceptions\ConflictException;
use App\Models\PowerBank;
use App\Models\Rental;
use DomainException;
use Illuminate\Support\Facades\DB;

class StartRentalService
{
    public function start(StartRentalData $data): Rental
    {
        return DB::transaction(function () use ($data): Rental {
            $powerBank = PowerBank::query()
                ->whereKey($data->powerBankId)
                ->lockForUpdate()
                ->first();

            if (! $powerBank) {
                throw new DomainException('Power bank not found.');
            }

            if ((int) $powerBank->pickup_point_id !== $data->pickupPointId) {
                throw new ConflictException('Power bank does not belong to the specified pickup point.');
            }

            $userHasOpenRental = Rental::query()
                ->where('user_id', $data->userId)
                ->whereIn('status', [
                    RentalStatus::ACTIVE->value,
                    RentalStatus::OVERDUE->value,
                ])
                ->exists();

            if ($userHasOpenRental) {
                throw new ConflictException('User already has an active or overdue rental.');
            }

            $startedAt = now();

            $rental = Rental::query()->create([
                'user_id' => $data->userId,
                'power_bank_id' => $data->powerBankId,
                'pickup_point_id' => $data->pickupPointId,
                'status' => RentalStatus::ACTIVE,
                'started_at' => $startedAt,
                'due_at' => $startedAt->copy()->addHours(4),
            ]);

            $updatedRows = PowerBank::query()
                ->whereKey($data->powerBankId)
                ->where('status', PowerBankStatus::AVAILABLE->value)
                ->update([
                    'status' => PowerBankStatus::RENTED->value,
                ]);

            if ($updatedRows === 0) {
                throw new ConflictException('Power bank is not available');
            }

            return $rental;
        });
    }
}
