<?php

use App\Http\Controllers\Billing\StripeWebhookController;
use App\Http\Controllers\Billing\SubscriptionCheckoutController;
use App\Http\Controllers\Billing\PurchaseCheckoutController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\ContactInformationController;
use App\Http\Controllers\Site\ContactInformationController as SiteContactInformationController;
use App\Http\Controllers\Site\ServiceController as SiteServiceController;
use App\Http\Controllers\Site\ContactMessageController;
use App\Http\Controllers\Site\PlanController as SitePlanController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\TemplateCatalogController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\ClientSubscriptionController;
use App\Http\Controllers\Client\ClientPurchaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::get('/templates', [TemplateCatalogController::class, 'index'])->name('templates.index');
Route::get('/templates/{template}', [TemplateCatalogController::class, 'show'])->name('templates.show');

Route::get('/downloads/{template}', DownloadController::class)->name('downloads.show');

Route::get('/purchases/verify/{sessionId}', [\App\Http\Controllers\Site\PurchaseController::class, 'verify'])->name('purchases.verify');
Route::post('/purchases/{purchase}/resend-link', [\App\Http\Controllers\Site\PurchaseController::class, 'resendLink'])->name('purchases.resend-link');

Route::get('/contact-information', [SiteContactInformationController::class, 'show'])->name('contact-information.show');
Route::get('/plans', [SitePlanController::class, 'index'])->name('plans.index');
Route::post('/contact-messages', [ContactMessageController::class, 'store'])->name('contact-messages.store');
Route::get('/services', [SiteServiceController::class, 'index'])->name('services.index');

// Verificar estado de verificación de email (público)
Route::post('/check-email-verification', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'verified' => false,
            'message' => 'No encontramos una cuenta con este correo electrónico.',
        ], 404);
    }

    return response()->json([
        'verified' => !!$user->email_verified_at,
        'email_verified_at' => $user->email_verified_at,
    ]);
})->name('check-email-verification');

// Checkout de compras (público, permite invitados)
Route::post('/checkout/purchase', PurchaseCheckoutController::class)->name('checkout.purchase');

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    // Ruta POST adicional para actualizar templates con archivos (method spoofing)
    Route::post('templates/{template}', [TemplateController::class, 'update'])->name('templates.update-post');
    Route::delete('templates/{template}/package-file', [TemplateController::class, 'deletePackageFile'])->name('templates.delete-package-file');
    Route::apiResource('templates', TemplateController::class);
    Route::apiResource('plans', PlanController::class);
    Route::apiResource('services', ServiceController::class);
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/reactivate', [SubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');
    Route::get('contact-information', [ContactInformationController::class, 'show'])->name('contact-information.show');
    Route::put('contact-information', [ContactInformationController::class, 'update'])->name('contact-information.update');
});

Route::middleware(['auth:sanctum', 'role:client'])->group(function (): void {
    Route::post('/plans/{plan}/checkout', [SitePlanController::class, 'checkout'])->name('plans.checkout');
    Route::post('/checkout/subscription', SubscriptionCheckoutController::class)->name('checkout.subscription');
});

Route::middleware(['auth:sanctum', 'role:client'])->prefix('client')->name('client.')->group(function (): void {
    Route::get('/subscriptions', [ClientController::class, 'subscriptions'])->name('subscriptions.index');
    Route::post('/subscriptions/{subscription}/cancel', [ClientSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/{subscription}/reactivate', [ClientSubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');

    Route::get('/purchases', [ClientController::class, 'purchases'])->name('purchases.index');
    Route::post('/purchases/{purchase}/resend-code', [ClientPurchaseController::class, 'resendCode'])->name('purchases.resend-code');
    Route::post('/purchases/{purchase}/validate-code', [ClientPurchaseController::class, 'validateCode'])->name('purchases.validate-code');

    Route::get('/licenses', [ClientController::class, 'licenses'])->name('licenses.index');
    Route::get('/downloads', [ClientController::class, 'downloads'])->name('downloads.index');
});
