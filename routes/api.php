<?php

use App\Http\Controllers\Billing\StripeWebhookController;
use App\Http\Controllers\Billing\SubscriptionCheckoutController;
use App\Http\Controllers\Billing\PurchaseCheckoutController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\TemplateCatalogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::get('/templates', [TemplateCatalogController::class, 'index'])->name('templates.index');
Route::get('/templates/{template}', [TemplateCatalogController::class, 'show'])->name('templates.show');

Route::get('/downloads/{template}', DownloadController::class)->name('downloads.show');

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::apiResource('templates', TemplateController::class);
});

Route::middleware(['auth:sanctum', 'role:client'])->group(function (): void {
    Route::post('/checkout/subscription', SubscriptionCheckoutController::class)->name('checkout.subscription');
    Route::post('/checkout/purchase', PurchaseCheckoutController::class)->name('checkout.purchase');
});
