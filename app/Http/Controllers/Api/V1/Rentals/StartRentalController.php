<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Rentals;

use App\DTO\Rentals\StartRentalData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rentals\StartRentalRequest;
use App\Services\Rentals\StartRentalService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class StartRentalController extends Controller
{
    public function __invoke(StartRentalRequest $request, StartRentalService $service): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            throw new UnauthorizedHttpException('Bearer');
        }

        $data = new StartRentalData(
            userId: (int) $user->getAuthIdentifier(),
            pickupPointId: $request->integer('pickup_point_id'),
            powerBankId: $request->integer('power_bank_id'),
        );

        $rental = $service->start($data);

        return response()->json([
            'rental_id' => $rental->id,
            'status' => $rental->status,
            'started_at' => $rental->started_at,
            'due_at' => $rental->due_at,
            'power_bank_id' => $rental->power_bank_id,
        ]);
    }
}
