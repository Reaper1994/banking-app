<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransferResource;
use App\Models\SavingsAccount;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

final class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $transferService
    ) {
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        try {
            $senderAccount = SavingsAccount::findOrFail($request->input('sender_account_id'));
            $recipientAccount = SavingsAccount::where('account_number', $request->input('recipient_account_number'))->firstOrFail();

            $transfer = $this->transferService->initiateTransfer(
                $senderAccount,
                $recipientAccount,
                (float) $request->input('amount'),
                $request->input('description')
            );

            return response()->json([
                'message' => 'Transfer completed successfully',
                'data' => new TransferResource($transfer),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing the transfer',
            ], 500);
        }
    }
} 