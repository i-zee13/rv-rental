<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\ClearController;
use App\Http\Controllers\MigrateController;

// ============================================================
// Public routes WITHOUT locale prefix (primary URLs)
// ============================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/vehicles/{id}', [VehicleController::class, 'show'])->name('vehicles.show');
Route::get('/pages/{slug}', [\App\Http\Controllers\PagePublicController::class, 'show'])->name('pages.show');
Route::get('/blog', [\App\Http\Controllers\BlogPublicController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogPublicController::class, 'show'])->name('blog.show');
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact');
Route::post('/leads', [\App\Http\Controllers\LeadController::class, 'store'])->name('leads.store');
Route::get('/leads/thank-you', [\App\Http\Controllers\LeadController::class, 'thankYou'])->name('leads.thank-you');

// ============================================================
// Public routes WITH locale prefix (/en/... or /es/...)
// ============================================================
Route::prefix('{locale}')
    ->where(['locale' => 'en|es'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index']);
        Route::get('/search', [SearchController::class, 'index']);
        Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
        Route::get('/pages/{slug}', [\App\Http\Controllers\PagePublicController::class, 'show']);
        Route::get('/blog', [\App\Http\Controllers\BlogPublicController::class, 'index']);
        Route::get('/blog/{slug}', [\App\Http\Controllers\BlogPublicController::class, 'show']);
    });

// ============================================================
// Locale switcher
// ============================================================
Route::get('/set-locale', [LocaleController::class, 'setLocale'])->name('set-locale');
Route::get('/clear', ClearController::class)->name('clear');
Route::get('/migrate', MigrateController::class)->name('migrate');

// ============================================================
// Auth routes
// ============================================================
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ============================================================
// Admin area
// ============================================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Vehicles
        Route::get('/vehicles', [AdminVehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/create', [AdminVehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [AdminVehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{id}/edit', [AdminVehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicles/{id}', [AdminVehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicleId}/images/{imageId}', [AdminVehicleController::class, 'destroyImage'])->name('vehicles.images.destroy');

        // Pages
        Route::get('/pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{id}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{id}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{id}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy');

        // Blog
        Route::get('/blog', [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/create', [\App\Http\Controllers\Admin\BlogController::class, 'create'])->name('blog.create');
        Route::post('/blog', [\App\Http\Controllers\Admin\BlogController::class, 'store'])->name('blog.store');
        Route::get('/blog/{id}/edit', [\App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('blog.edit');
        Route::put('/blog/{id}', [\App\Http\Controllers\Admin\BlogController::class, 'update'])->name('blog.update');
        Route::delete('/blog/{id}', [\App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('blog.destroy');

        // Add-ons (booking extras)
        Route::get('/addons', [\App\Http\Controllers\Admin\AddonController::class, 'index'])->name('addons.index');
        Route::get('/addons/create', [\App\Http\Controllers\Admin\AddonController::class, 'create'])->name('addons.create');
        Route::post('/addons', [\App\Http\Controllers\Admin\AddonController::class, 'store'])->name('addons.store');
        Route::get('/addons/{id}/edit', [\App\Http\Controllers\Admin\AddonController::class, 'edit'])->name('addons.edit');
        Route::put('/addons/{id}', [\App\Http\Controllers\Admin\AddonController::class, 'update'])->name('addons.update');
        Route::delete('/addons/{id}', [\App\Http\Controllers\Admin\AddonController::class, 'destroy'])->name('addons.destroy');

        // Bookings
        Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{id}/status', [\App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.status');

        // SEO
        Route::get('/seo', [\App\Http\Controllers\Admin\SeoController::class, 'index'])->name('seo.index');
        Route::get('/seo/{id}/edit', [\App\Http\Controllers\Admin\SeoController::class, 'edit'])->name('seo.edit');
        Route::put('/seo/{id}', [\App\Http\Controllers\Admin\SeoController::class, 'update'])->name('seo.update');

        // Leads
        Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{id}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('leads.show');
        Route::post('/leads/{id}/status', [\App\Http\Controllers\Admin\LeadController::class, 'updateStatus'])->name('leads.status');
    });
});

// ============================================================
// Booking flow (public, no locale prefix)
// ============================================================
Route::get('/booking/step1', [\App\Http\Controllers\BookingController::class, 'step1'])->name('booking.step1');
Route::post('/booking/step1', [\App\Http\Controllers\BookingController::class, 'postStep1'])->name('booking.postStep1');
Route::get('/booking/step2', [\App\Http\Controllers\BookingController::class, 'step2'])->name('booking.step2');
Route::post('/booking/step2', [\App\Http\Controllers\BookingController::class, 'postStep2'])->name('booking.postStep2');
Route::get('/booking/step3', [\App\Http\Controllers\BookingController::class, 'step3'])->name('booking.step3');
Route::post('/booking/step3', [\App\Http\Controllers\BookingController::class, 'postStep3'])->name('booking.postStep3');
Route::get('/booking/step4', [\App\Http\Controllers\BookingController::class, 'step4'])->name('booking.step4');
Route::post('/booking/confirm', [\App\Http\Controllers\BookingController::class, 'confirm'])->name('booking.confirm');
Route::post('/booking/create-checkout', [\App\Http\Controllers\BookingController::class, 'createCheckout'])->name('booking.createCheckout');
