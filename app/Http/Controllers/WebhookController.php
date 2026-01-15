<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected TelegramService $telegram
    ) {}

    /**
     * Handle Sentry webhook alerts
     */
    public function sentry(Request $request): JsonResponse
    {
        // Verify webhook secret if configured
        $secret = config('telegram.sentry_webhook_secret');
        if ($secret && $request->header('sentry-hook-signature') !== $secret) {
            // For simple verification, check a query param or header
            $providedSecret = $request->query('secret') ?? $request->header('X-Sentry-Token');
            if ($providedSecret !== $secret) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        $data = $request->all();

        // Handle different Sentry webhook event types
        $resource = $data['action'] ?? 'triggered';

        if ($resource === 'triggered' || $resource === 'created') {
            $this->telegram->notifySentryError($data);
        }

        return response()->json(['status' => 'ok']);
    }
}
