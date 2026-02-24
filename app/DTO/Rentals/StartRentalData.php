<?php

declare(strict_types=1);

namespace App\DTO\Rentals;

readonly class StartRentalData
{
    public function __construct(
        public int $userId,
        public int $pickupPointId,
        public int $powerBankId,
    ) {
    }
}
