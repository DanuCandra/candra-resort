<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Guest\DashboardController as GuestDashboardController;
use App\Http\Controllers\Guest\PaymentController as GuestPaymentController;
use App\Http\Controllers\Guest\ReservationController as GuestReservationController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ReceptionistController as OwnerReceptionistController;
use App\Http\Controllers\Owner\ReportController as OwnerReportController;
use App\Http\Controllers\Payment\MidtransNotificationController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\RoomController;
use App\Http\Controllers\Receptionist\CheckInController;
use App\Http\Controllers\Receptionist\CheckOutController;
use App\Http\Controllers\Receptionist\DashboardController as ReceptionistDashboardController;
use App\Http\Controllers\Receptionist\FacilityController;
use App\Http\Controllers\Receptionist\FolioController;
use App\Http\Controllers\Receptionist\FoodCategoryController;
use App\Http\Controllers\Receptionist\FoodOrderController as ReceptionistFoodOrderController;
use App\Http\Controllers\Receptionist\GuestController as ReceptionistGuestController;
use App\Http\Controllers\Receptionist\GuestRequestController as ReceptionistGuestRequestController;
use App\Http\Controllers\Receptionist\HotelServiceController;
use App\Http\Controllers\Receptionist\MenuItemController;
use App\Http\Controllers\Receptionist\PaymentController as ReceptionistPaymentController;
use App\Http\Controllers\Receptionist\PaymentMethodController;
use App\Http\Controllers\Receptionist\PromotionController;
use App\Http\Controllers\Receptionist\ReservationController as ReceptionistReservationController;
use App\Http\Controllers\Receptionist\RoomController as ReceptionistRoomController;
use App\Http\Controllers\Receptionist\RoomRateController;
use App\Http\Controllers\Receptionist\RoomTypeController;
use App\Http\Controllers\Receptionist\ServiceOrderController as ReceptionistServiceOrderController;
use App\Http\Controllers\Receptionist\WebsiteContentController as ReceptionistWebsiteContentController;
use App\Http\Controllers\RoomService\AccessController;
use App\Http\Controllers\RoomService\BillController;
use App\Http\Controllers\RoomService\FoodOrderController as RoomServiceFoodOrderController;
use App\Http\Controllers\RoomService\GuestRequestController as RoomServiceGuestRequestController;
use App\Http\Controllers\RoomService\ServiceOrderController as RoomServiceServiceOrderController;
use App\Http\Controllers\Staff\ProfileController as StaffProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/rooms', [RoomController::class, 'index'])->name('public.rooms.index');
Route::get('/rooms/{roomType}', [RoomController::class, 'show'])->name('public.rooms.show');
Route::get('/about', [PageController::class, 'about'])->name('public.about');
Route::get('/facilities', [PageController::class, 'facilities'])->name('public.facilities');
Route::get('/gallery', [PageController::class, 'gallery'])->name('public.gallery');
Route::get('/promotions', [PageController::class, 'promotions'])->name('public.promotions.index');
Route::get('/contact', [PageController::class, 'contact'])->name('public.contact');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/payments/midtrans/notification', MidtransNotificationController::class)->name('payments.midtrans.notification');

Route::prefix('room-service')->name('room-service.')->group(function (): void {
    Route::middleware('room.access')->group(function (): void {
        Route::get('/portal', [AccessController::class, 'home'])->name('home');
        Route::post('/logout', [AccessController::class, 'destroy'])->name('logout');
        Route::get('/food', [RoomServiceFoodOrderController::class, 'index'])->name('food.index');
        Route::post('/food/orders', [RoomServiceFoodOrderController::class, 'store'])->name('food.store');
        Route::get('/food/orders', [RoomServiceFoodOrderController::class, 'orders'])->name('food.orders');
        Route::get('/food/orders/{foodOrder}', [RoomServiceFoodOrderController::class, 'show'])->name('food.show');
        Route::get('/services', [RoomServiceServiceOrderController::class, 'index'])->name('services.index');
        Route::post('/services/orders', [RoomServiceServiceOrderController::class, 'store'])->name('services.store');
        Route::get('/services/orders', [RoomServiceServiceOrderController::class, 'orders'])->name('services.orders');
        Route::get('/services/orders/{serviceOrder}', [RoomServiceServiceOrderController::class, 'show'])->name('services.show');
        Route::get('/requests', [RoomServiceGuestRequestController::class, 'index'])->name('requests.index');
        Route::post('/requests', [RoomServiceGuestRequestController::class, 'store'])->name('requests.store');
        Route::get('/requests/{guestRequest}', [RoomServiceGuestRequestController::class, 'show'])->name('requests.show');
        Route::get('/bill', BillController::class)->name('bill.show');
    });
    Route::get('/{qrToken}', [AccessController::class, 'show'])->name('verify');
    Route::post('/{qrToken}', [AccessController::class, 'verify'])->middleware('throttle:10,1')->name('verify.store');
});

Route::prefix('guest')->name('guest.')->middleware(['auth', 'active', 'role:guest'])->group(function (): void {
    Route::get('/dashboard', GuestDashboardController::class)->name('dashboard');
    Route::get('/reservations', [GuestReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create/{roomType}', [GuestReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [GuestReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [GuestReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [GuestReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::get('/reservations/{reservation}/payment', [GuestPaymentController::class, 'show'])->name('reservations.payment');
});

Route::prefix('receptionist')->name('receptionist.')->middleware(['auth', 'active', 'role:receptionist'])->group(function (): void {
    Route::get('/dashboard', ReceptionistDashboardController::class)->name('dashboard');
    Route::get('/profile', [StaffProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [StaffProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/guests', [ReceptionistGuestController::class, 'index'])->name('guests.index');
    Route::get('/guests/accounts/{guest}', [ReceptionistGuestController::class, 'showAccount'])->name('guests.accounts.show');
    Route::get('/guests/walk-ins/{stay}', [ReceptionistGuestController::class, 'showWalkIn'])->name('guests.walk-ins.show');

    Route::get('/reservations', [ReceptionistReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/walk-in', [ReceptionistReservationController::class, 'createWalkIn'])->name('reservations.walk-in.create');
    Route::post('/reservations/walk-in', [ReceptionistReservationController::class, 'storeWalkIn'])->name('reservations.walk-in.store');
    Route::get('/reservations/{reservation}', [ReceptionistReservationController::class, 'show'])->name('reservations.show');
    Route::get('/check-in', [CheckInController::class, 'index'])->name('checkin.index');
    Route::get('/check-in/{reservation}', [CheckInController::class, 'create'])->name('checkin.create');
    Route::post('/check-in/{reservation}', [CheckInController::class, 'store'])->name('checkin.store');
    Route::get('/stays/{stay}/identity', [CheckInController::class, 'identity'])->name('stays.identity');
    Route::get('/check-out', [CheckOutController::class, 'index'])->name('checkout.index');
    Route::get('/check-out/{stay}', [CheckOutController::class, 'create'])->name('checkout.create');
    Route::post('/check-out/{stay}', [CheckOutController::class, 'store'])->name('checkout.store');
    Route::get('/folios/{folio}', [FolioController::class, 'show'])->name('folios.show');
    Route::get('/folios', [FolioController::class, 'index'])->name('folios.index');
    Route::post('/folios/{folio}/payments', [FolioController::class, 'recordPayment'])->name('folios.payments.store');

    Route::resource('facilities', FacilityController::class)->except('show');
    Route::resource('room-types', RoomTypeController::class);
    Route::post('/room-types/{room_type}/images/{image}/primary', [RoomTypeController::class, 'setPrimaryImage'])->name('room-types.images.primary');
    Route::delete('/room-types/{room_type}/images/{image}', [RoomTypeController::class, 'destroyImage'])->name('room-types.images.destroy');

    Route::get('/rooms/{room}/qr', [ReceptionistRoomController::class, 'qr'])->name('rooms.qr');
    Route::get('/rooms/{room}/qr/image', [ReceptionistRoomController::class, 'qrImage'])->name('rooms.qr.image');
    Route::get('/rooms/{room}/qr/print', [ReceptionistRoomController::class, 'qrPrint'])->name('rooms.qr.print');
    Route::post('/rooms/{room}/qr/regenerate', [ReceptionistRoomController::class, 'regenerateQr'])->name('rooms.qr.regenerate');
    Route::resource('rooms', ReceptionistRoomController::class);

    Route::resource('pricing', RoomRateController::class)->except('show');
    Route::resource('promotions', PromotionController::class)->except('show');
    Route::resource('payment-methods', PaymentMethodController::class)->except('show');
    Route::get('/payments', [ReceptionistPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [ReceptionistPaymentController::class, 'show'])->name('payments.show');
    Route::resource('food-categories', FoodCategoryController::class)->except('show');
    Route::resource('menu-items', MenuItemController::class)->except('show');
    Route::get('/food-orders', [ReceptionistFoodOrderController::class, 'index'])->name('food-orders.index');
    Route::get('/food-orders/{foodOrder}', [ReceptionistFoodOrderController::class, 'show'])->name('food-orders.show');
    Route::post('/food-orders/{foodOrder}/status', [ReceptionistFoodOrderController::class, 'updateStatus'])->name('food-orders.status');
    Route::resource('hotel-services', HotelServiceController::class)->except('show');
    Route::get('/service-orders', [ReceptionistServiceOrderController::class, 'index'])->name('service-orders.index');
    Route::get('/service-orders/{serviceOrder}', [ReceptionistServiceOrderController::class, 'show'])->name('service-orders.show');
    Route::post('/service-orders/{serviceOrder}/status', [ReceptionistServiceOrderController::class, 'updateStatus'])->name('service-orders.status');
    Route::get('/guest-requests', [ReceptionistGuestRequestController::class, 'index'])->name('guest-requests.index');
    Route::get('/guest-requests/{guestRequest}', [ReceptionistGuestRequestController::class, 'show'])->name('guest-requests.show');
    Route::post('/guest-requests/{guestRequest}/status', [ReceptionistGuestRequestController::class, 'updateStatus'])->name('guest-requests.status');

    Route::get('/website', [ReceptionistWebsiteContentController::class, 'index'])->name('website.index');
    Route::put('/website/settings', [ReceptionistWebsiteContentController::class, 'updateSettings'])->name('website.settings.update');
    Route::post('/website/contents', [ReceptionistWebsiteContentController::class, 'storeContent'])->name('website.contents.store');
    Route::put('/website/contents/{websiteContent}', [ReceptionistWebsiteContentController::class, 'updateContent'])->name('website.contents.update');
    Route::delete('/website/contents/{websiteContent}', [ReceptionistWebsiteContentController::class, 'destroyContent'])->name('website.contents.destroy');
    Route::post('/website/gallery', [ReceptionistWebsiteContentController::class, 'storeGallery'])->name('website.gallery.store');
    Route::put('/website/gallery/{galleryImage}', [ReceptionistWebsiteContentController::class, 'updateGallery'])->name('website.gallery.update');
    Route::delete('/website/gallery/{galleryImage}', [ReceptionistWebsiteContentController::class, 'destroyGallery'])->name('website.gallery.destroy');
});

Route::prefix('owner')->name('owner.')->middleware(['auth', 'active', 'role:owner'])->group(function (): void {
    Route::get('/dashboard', OwnerDashboardController::class)->name('dashboard');
    Route::get('/profile', [StaffProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [StaffProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/reports/reservations', [OwnerReportController::class, 'reservations'])->name('reports.reservations');
    Route::get('/reports/occupancy', [OwnerReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('/reports/revenue', [OwnerReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/payments', [OwnerReportController::class, 'payments'])->name('reports.payments');
    Route::get('/reports/services', [OwnerReportController::class, 'services'])->name('reports.services');
    Route::get('/reports/monthly', [OwnerReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/{report}/export', [OwnerReportController::class, 'export'])
        ->whereIn('report', ['reservations', 'occupancy', 'revenue', 'payments', 'services', 'monthly'])
        ->name('reports.export');
    Route::post('/receptionists/{receptionist}/toggle', [OwnerReceptionistController::class, 'toggle'])->name('receptionists.toggle');
    Route::post('/receptionists/{receptionist}/reset-password', [OwnerReceptionistController::class, 'resetPassword'])->name('receptionists.reset-password');
    Route::resource('receptionists', OwnerReceptionistController::class);
});
