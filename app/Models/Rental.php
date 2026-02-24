<?php

namespace App\Models;

use App\Domain\Rental\Enums\RentalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'power_bank_id',
        'pickup_point_id',
        'status',
        'started_at',
        'due_at',
        'returned_at',
        'total_amount',
        'penalty_amount',
        'penalty_cents',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RentalStatus::class,
            'started_at' => 'datetime',
            'due_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }
}
