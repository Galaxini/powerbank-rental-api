<?php

declare(strict_types=1);

namespace App\Domain\PowerBank\Enums;

/**
 * Defines the lifecycle status values for a power bank unit.
 */
enum PowerBankStatus: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case MAINTENANCE = 'maintenance';
    case LOST = 'lost';
}
