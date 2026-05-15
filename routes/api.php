<?php

use App\Modules\Automation\Controllers\AutomationController;
use App\Modules\Core\Auth\Controllers\AuthController;
use App\Modules\CRM\Controllers\AppointmentController;
use App\Modules\CRM\Controllers\BusinessController;
use App\Modules\CRM\Controllers\LeadController;
use App\Modules\Messaging\Controllers\ConversationController;
use App\Modules\Messaging\Controllers\MessageController;
use App\Modules\Messaging\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\GroqChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Chat endpoint is protected (requires Sanctum auth). Rate-limited.

    Route::prefix('/webhooks/whatsapp')->middleware('throttle:120,1')->group(function () {
        Route::post('/incoming', [WhatsAppWebhookController::class, 'incoming']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/chat/groq', [GroqChatController::class, 'chat'])->middleware('throttle:groq');
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('businesses', BusinessController::class);
        Route::apiResource('leads', LeadController::class);
        Route::apiResource('appointments', AppointmentController::class);
        Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm']);
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule']);

        Route::apiResource('conversations', ConversationController::class);
        Route::apiResource('automations', AutomationController::class);

        Route::get('/messages', [MessageController::class, 'index']);
        Route::post('/messages', [MessageController::class, 'store']);
    });
});
