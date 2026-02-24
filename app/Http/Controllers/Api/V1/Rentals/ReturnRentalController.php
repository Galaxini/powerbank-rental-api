<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Rentals;

use App\Domain\Rental\Services\ReturnRentalService;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ReturnRentalController extends Controller
{
    public function __invoke(Request $request, Rental $rental, ReturnRentalService $service): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            throw new UnauthorizedHttpException('Bearer');
        }

        try {
            $result = $service->handle($rental, (int) $user->getAuthIdentifier());
        } catch (DomainException $exception) {
            $status = in_array($exception->getCode(), [403, 404, 409], true) ? $exception->getCode() : 409;

            return response()->json([
                'message' => $exception->getMessage(),
            ], $status);
        }

        /** @var Rental $returnedRental */
        $returnedRental = $result['rental'];

        return response()->json([
            'rental_id' => $returnedRental->id,
            'status' => $returnedRental->status->value,
            'returned_at' => $returnedRental->returned_at,
            'overdue_minutes' => $result['overdue_minutes'],
            'penalty_cents' => $result['penalty_cents'],
            'power_bank_id' => $returnedRental->power_bank_id,
        ]);
    }
}
