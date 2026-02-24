<?php

namespace App\Models;

use App\Domain\PowerBank\Enums\PowerBankStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerBank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'serial_number',
        'pickup_point_id',
        'status',
        'battery_level',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PowerBankStatus::class,
        ];
    }
}
