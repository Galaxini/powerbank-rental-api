<?php

declare(strict_types=1);

namespace App\Domain\Rental\Enums;

/**
 * Defines the status values for a rental transaction.
 */
enum RentalStatus: string
{
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
    case LOST = 'lost';
}
