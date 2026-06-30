<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RoleRegisterController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\LandlordApprovalController;
use App\Http\Controllers\Landlord\LandlordDashboardController;
use App\Http\Controllers\Landlord\LandlordRoomController;
use App\Http\Controllers\Landlord\LandlordBookingController;

use App\Http\Controllers\Student\RoomBrowseController;
use App\Http\Controllers\Student\SavedRoomController;
use App\Http\Controllers\Student\StudentPasswordController;

// ✅ ADDED (Booking + Payment)
use App\Http\Controllers\Student\BookingController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\ContractController;

use App\Http\Controllers\Admin\AdminDisputeController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AnnouncementController;

// ✅ ADDED (Student Notifications Controller)
use App\Http\Controllers\Student\StudentNotificationController;
use App\Http\Controllers\Student\StudentAccountController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\Student\MonthlyRentController;
use App\Http\Controllers\Landlord\LandlordPaymentController;
use App\Http\Controllers\Landlord\LandlordNotificationController;
use App\Http\Controllers\Student\ReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\Landlord\LandlordProfileController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Register (CUSTOM roles) - Guest only
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    Route::get('/register', [RoleRegisterController::class, 'choose'])
        ->name('register');

    Route::get('/register/student', [RoleRegisterController::class, 'studentForm'])
        ->name('register.student');

    Route::post('/register/student', [RoleRegisterController::class, 'studentStore'])
        ->name('register.student.store');

    Route::get('/register/landlord', [RoleRegisterController::class, 'landlordForm'])
        ->name('register.landlord');

    Route::post('/register/landlord', [RoleRegisterController::class, 'landlordStore'])
        ->name('register.landlord.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (ANY logged-in user)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Breeze profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rooms (public/general page if you use it)
    Route::get('/rooms', fn () => view('rooms.index'))->name('rooms.index');

    /*
    |--------------------------------------------------------------------------
    | ✅ STUDENT PORTAL
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:student')->group(function () {

        // Student home/dashboard
        Route::get('/student/dashboard', fn () => view('student.dashboard'))
            ->name('student.dashboard');

        // Rooms browsing
        Route::get('/student/rooms', [RoomBrowseController::class, 'index'])
            ->name('student.rooms.index');

        Route::get('/student/rooms/{room}', [RoomBrowseController::class, 'show'])
            ->name('student.rooms.show');

        // Help/About/Contact
        Route::get('/student/help', fn () => view('student.help'))
            ->name('student.help');
        
        

        // ✅ Bookings (CONTROLLER)
        Route::get('/student/bookings', [BookingController::class, 'index'])
            ->name('student.bookings.index');

        Route::get('/student/rooms/{room}/book', [BookingController::class, 'create'])
            ->name('student.bookings.create');

        Route::post('/student/rooms/{room}/book', [BookingController::class, 'store'])
            ->name('student.bookings.store');

        // ✅ Booking Details
        Route::get('/student/bookings/{booking}', [BookingController::class, 'show'])
            ->name('student.bookings.show');

        // ✅ Cancel booking (pending only)
        Route::post('/student/bookings/{booking}/cancel', [BookingController::class, 'cancel'])
            ->name('student.bookings.cancel');

        // ✅ Request Cancel/Refund (payment_submitted / paid)
        Route::post('/student/bookings/{booking}/request-cancel', [BookingController::class, 'requestCancel'])
            ->name('student.bookings.request_cancel');

        // ✅ Payment History / Transaction History (CONTROLLER)
        Route::get('/student/payments/history', [PaymentController::class, 'index'])
            ->name('student.payments.index');

        Route::get('/student/payments/{booking}', [PaymentController::class, 'show'])
            ->name('student.payments.show');

        Route::post('/student/payments/{booking}/upload', [PaymentController::class, 'upload'])
            ->name('student.payments.upload');

        Route::get('/student/messages', [ChatController::class, 'studentIndex'])
           ->name('student.messages.index');

        Route::get('/student/messages/data', [ChatController::class, 'studentData'])
            ->name('student.messages.data');

        Route::post('/student/messages/{booking}', [ChatController::class, 'studentStore'])
            ->name('student.messages.store');

        Route::patch('/student/messages/{message}', [ChatController::class, 'studentUpdate'])
            ->name('student.messages.update');

        Route::delete('/student/messages/{message}', [ChatController::class, 'studentDestroy'])
            ->name('student.messages.destroy');

        Route::get('/student/chat', fn () => redirect()->route('student.messages.index'))
            ->name('student.chat.index');

        // ✅ Saved Rooms
        Route::get('/student/bookmarks', [SavedRoomController::class, 'index'])
            ->name('student.bookmarks.index');

        Route::post('/student/bookmarks/{room}', [SavedRoomController::class, 'toggle'])
            ->name('student.bookmarks.toggle');

        Route::post('/student/bookings/{booking}/review', [ReviewController::class, 'store'])
           ->name('student.reviews.store');

        // Student Profile page
      Route::get('/student/account', [StudentAccountController::class, 'edit'])
    ->name('student.account');

      Route::post('/student/account', [StudentAccountController::class, 'update'])
    ->name('student.account.update');
        /*
        |--------------------------------------------------------------------------
        | ✅ NOTIFICATIONS (REAL)  ✅ ONLY THIS PART CHANGED
        |--------------------------------------------------------------------------
        */

        Route::get('/student/notifications', [StudentNotificationController::class, 'index'])
            ->name('student.notifications.index');

        Route::post('/student/notifications/read', [StudentNotificationController::class, 'read'])
            ->name('student.notifications.read');

        Route::post('/student/notifications/read-all', [StudentNotificationController::class, 'readAll'])
            ->name('student.notifications.read_all');

        Route::delete('/student/notifications/clear-all', [StudentNotificationController::class, 'clearAll'])
            ->name('student.notifications.clear_all');

        Route::delete('/student/notifications/delete-selected', [StudentNotificationController::class, 'deleteSelected'])
            ->name('student.notifications.delete_selected');

        Route::delete('/student/notifications/{id}', [StudentNotificationController::class, 'destroy'])
            ->name('student.notifications.destroy');

                /*
        |--------------------------------------------------------------------------
        | STUDENT CONTRACTS
        |--------------------------------------------------------------------------
        */
        Route::get('/student/contracts', [ContractController::class, 'index'])
            ->name('student.contracts.index');

        Route::get('/student/contracts/{contract}', [ContractController::class, 'show'])
            ->name('student.contracts.show');

        Route::post('/student/contracts/{contract}/sign', [ContractController::class, 'sign'])
            ->name('student.contracts.sign');

        Route::get('/student/contracts/{contract}/pdf', [ContractController::class, 'downloadPdf'])
            ->name('student.contracts.pdf');

        Route::get('/student/change-password', [StudentPasswordController::class, 'edit'])
        ->name('student.password.edit');

        Route::post('/student/change-password', [StudentPasswordController::class, 'update'])
        ->name('student.password.update');


            /*
        |--------------------------------------------------------------------------
        | STUDENT MONTHLY RENTS
        |--------------------------------------------------------------------------
        */
        Route::get('/student/monthly-rents', [MonthlyRentController::class, 'index'])
            ->name('student.monthly-rents.index');

        Route::get('/student/monthly-rents/{monthlyRent}', [MonthlyRentController::class, 'show'])
            ->name('student.monthly-rents.show');

        Route::post('/student/monthly-rents/{monthlyRent}/upload', [MonthlyRentController::class, 'upload'])
            ->name('student.monthly-rents.upload');

        Route::get('/student/bookings/{booking}/dispute', [DisputeController::class, 'studentCreate'])
            ->name('student.disputes.create');

        Route::post('/student/bookings/{booking}/dispute', [DisputeController::class, 'studentStore'])
            ->name('student.disputes.store');
                
    });

    /*
    |--------------------------------------------------------------------------
    | LANDLORD status pages
    |--------------------------------------------------------------------------
    */
    Route::get('/landlord/pending', fn () => view('landlord.pending'))
        ->middleware('role:landlord')
        ->name('landlord.pending');

    Route::get('/landlord/rejected', fn () => view('landlord.rejected'))
        ->middleware('role:landlord')
        ->name('landlord.rejected');

    /*
    |--------------------------------------------------------------------------
    | LANDLORD (Approved only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:landlord', 'landlord.approved'])->group(function () {

        Route::get('/landlord/dashboard', [LandlordDashboardController::class, 'index'])
            ->name('landlord.dashboard');

        // Room CRUD
        Route::get('/landlord/rooms', [LandlordRoomController::class, 'index'])
            ->name('landlord.rooms.index');

        Route::get('/landlord/rooms/create', [LandlordRoomController::class, 'create'])
            ->name('landlord.rooms.create');

        Route::post('/landlord/rooms', [LandlordRoomController::class, 'store'])
            ->name('landlord.rooms.store');

        Route::get('/landlord/rooms/{room}/edit', [LandlordRoomController::class, 'edit'])
            ->name('landlord.rooms.edit');

        Route::put('/landlord/rooms/{room}', [LandlordRoomController::class, 'update'])
            ->name('landlord.rooms.update');

        // publish / unpublish
        Route::post('/landlord/rooms/{room}/publish', [LandlordRoomController::class, 'publish'])
            ->name('landlord.rooms.publish');

        Route::post('/landlord/rooms/{room}/unpublish', [LandlordRoomController::class, 'unpublish'])
            ->name('landlord.rooms.unpublish');

        // delete room
        Route::delete('/landlord/rooms/{room}', [LandlordRoomController::class, 'destroy'])
            ->name('landlord.rooms.destroy');

        // images
        Route::post('/landlord/rooms/{room}/images/{image}/cover', [LandlordRoomController::class, 'setCover'])
            ->name('landlord.rooms.images.cover');

        Route::delete('/landlord/rooms/{room}/images/{image}', [LandlordRoomController::class, 'destroyImage'])
            ->name('landlord.rooms.images.destroy');

        // placeholders
        Route::get('/landlord/bookings', [LandlordBookingController::class, 'index'])
             ->name('landlord.bookings.index');

        Route::get('/landlord/notifications', [LandlordNotificationController::class, 'index'])
            ->name('landlord.notifications.index');

        Route::post('/landlord/notifications/read', [LandlordNotificationController::class, 'read'])
            ->name('landlord.notifications.read');

        Route::post('/landlord/notifications/read-all', [LandlordNotificationController::class, 'readAll'])
            ->name('landlord.notifications.read_all');

        Route::delete('/landlord/notifications/clear-all', [LandlordNotificationController::class, 'clearAll'])
            ->name('landlord.notifications.clear_all');

        Route::delete('/landlord/notifications/delete-selected', [LandlordNotificationController::class, 'deleteSelected'])
            ->name('landlord.notifications.delete_selected');

        Route::delete('/landlord/notifications/{id}', [LandlordNotificationController::class, 'destroy'])
            ->name('landlord.notifications.destroy');

        Route::get('/landlord/payments', [LandlordPaymentController::class, 'index'])
            ->name('landlord.payments.index');

        Route::post('/landlord/payments/{payment}/approve', [LandlordPaymentController::class, 'approve'])
            ->name('landlord.payments.approve');

        Route::post('/landlord/payments/{payment}/reject', [LandlordPaymentController::class, 'reject'])
            ->name('landlord.payments.reject');

        Route::get('/landlord/messages', [ChatController::class, 'landlordIndex'])
    ->name('landlord.messages.index');

        Route::get('/landlord/messages/data', [ChatController::class, 'landlordData'])
            ->name('landlord.messages.data');

        Route::post('/landlord/messages/{booking}', [ChatController::class, 'landlordStore'])
            ->name('landlord.messages.store');

        Route::patch('/landlord/messages/{message}', [ChatController::class, 'landlordUpdate'])
            ->name('landlord.messages.update');

        Route::delete('/landlord/messages/{message}', [ChatController::class, 'landlordDestroy'])
            ->name('landlord.messages.destroy');
        
     Route::post('/landlord/monthly-rents/{monthlyRent}/approve', [LandlordPaymentController::class, 'approveMonthly'])
    ->name('landlord.monthly-rents.approve');

    Route::post('/landlord/monthly-rents/{monthlyRent}/reject', [LandlordPaymentController::class, 'rejectMonthly'])
    ->name('landlord.monthly-rents.reject');


    Route::get('/landlord/bookings/{booking}/dispute', [DisputeController::class, 'landlordCreate'])
    ->name('landlord.disputes.create');

    Route::post('/landlord/bookings/{booking}/dispute', [DisputeController::class, 'landlordStore'])
    ->name('landlord.disputes.store');

    Route::get('/landlord/payments/{sourceType}/{sourceId}/summary-pdf', [LandlordPaymentController::class, 'summaryPdf'])
    ->name('landlord.payments.summary-pdf');


    Route::middleware(['auth'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/profile', [LandlordProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [LandlordProfileController::class, 'update'])->name('profile.update');
});
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (ADMIN ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', fn () => view('admin.dashboard'))
        ->name('admin.dashboard');

    // Pending approvals (existing page)
    Route::get('/admin/landlords', [LandlordApprovalController::class, 'index'])
        ->name('admin.landlords.index');

    Route::post('/admin/landlords/{user}/approve', [LandlordApprovalController::class, 'approve'])
        ->name('admin.landlords.approve');

    Route::post('/admin/landlords/{user}/reject', [LandlordApprovalController::class, 'reject'])
        ->name('admin.landlords.reject');

    // ✅ NEW — All landlords (Pending / Approved / Rejected)
    Route::get('/admin/landlords/all', [LandlordApprovalController::class, 'all'])
        ->name('admin.landlords.all');

    // ✅ Verify Listings (Rooms)
    Route::get('/admin/listings/verify', [\App\Http\Controllers\Admin\ListingVerificationController::class, 'index'])
        ->name('admin.listings.verify');

    Route::post('/admin/listings/{room}/approve', [\App\Http\Controllers\Admin\ListingVerificationController::class, 'approve'])
        ->name('admin.listings.approve');

    Route::post('/admin/listings/{room}/reject', [\App\Http\Controllers\Admin\ListingVerificationController::class, 'reject'])
        ->name('admin.listings.reject');

    // ✅ NEW: Manage Users (REAL)
    Route::get('/admin/users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])
        ->name('admin.users.index');

    Route::get('/admin/users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'show'])
        ->name('admin.users.show');

    Route::get('/admin/disputes', [AdminDisputeController::class, 'index'])->name('admin.disputes.index');
    Route::get('/admin/disputes/{dispute}', [AdminDisputeController::class, 'show'])->name('admin.disputes.show');

    Route::post('/admin/disputes/{dispute}/status', [AdminDisputeController::class, 'updateStatus'])->name('admin.disputes.status');
    Route::post('/admin/disputes/{dispute}/note', [AdminDisputeController::class, 'saveAdminNote'])->name('admin.disputes.note');

    Route::post('/admin/disputes/{dispute}/resolve', [AdminDisputeController::class, 'resolve'])->name('admin.disputes.resolve');
    Route::post('/admin/disputes/{dispute}/reject', [AdminDisputeController::class, 'reject'])->name('admin.disputes.reject');

    Route::post('/admin/disputes/{dispute}/evidence', [AdminDisputeController::class, 'uploadEvidence'])->name('admin.disputes.evidence');

    Route::get('/admin/reports', [AdminReportController::class, 'index'])
        ->name('admin.reports.index');

    Route::get('/admin/announcements', [AnnouncementController::class,'index'])
        ->name('admin.announcements.index');

    Route::get('/admin/announcements/create', [AnnouncementController::class,'create'])
        ->name('admin.announcements.create');

    Route::post('/admin/announcements', [AnnouncementController::class,'store'])
        ->name('admin.announcements.store');

    Route::delete('/admin/announcements/{announcement}', [AnnouncementController::class,'destroy'])
        ->name('admin.announcements.destroy');

    Route::get('/admin/maintenance', fn () => view('admin.maintenance.index'))->name('admin.maintenance.index');

});

/*
|--------------------------------------------------------------------------
| Breeze Auth routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Override Logout Redirect (Logout -> /register)
|--------------------------------------------------------------------------
*/
Route::post('/logout', function (Request $request) {

    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('register');

})->middleware('auth')->name('logout');