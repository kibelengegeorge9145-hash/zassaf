<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AdministratorController as AdminAdministratorController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\MembershipSettingController as AdminMembershipSettingController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\PasswordResetController as AdminPasswordResetController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\WeekendConvoController as AdminWeekendConvoController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactPageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Member\AuthController as MemberAuthController;
use App\Http\Controllers\Member\BookController as MemberBookController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\MembershipController as MemberMembershipController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\WeekendConvoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/programs', [ProgramController::class, 'index'])->name('programs');
Route::get('/weekend-convo', [WeekendConvoController::class, 'index'])->name('weekend-convo');
Route::get('/books', [BookController::class, 'index'])->name('books');
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');

Route::get('/books/{book:slug}/purchase', [BookController::class, 'purchase'])->name('books.purchase');
Route::post('/books/{book:slug}/guest-checkout', [BookController::class, 'guestCheckout'])->name('books.guest.checkout');
Route::get('/guest-download/{token}', [BookController::class, 'guestDownload'])->name('guest.download');

Route::get('/books/{book:slug}/checkout', [MemberBookController::class, 'checkout'])->name('books.checkout')->middleware('member');
Route::post('/books/{book:slug}/buy', [MemberBookController::class, 'buy'])->name('books.buy')->middleware('member');
Route::get('/books/{book:slug}/read', [MemberBookController::class, 'read'])->name('books.read')->middleware('member');
Route::get('/books/{book:slug}/download', [MemberBookController::class, 'download'])->name('books.download')->middleware('member');
Route::get('/community', [CommunityController::class, 'index'])->name('community');
Route::get('/membership', [MembershipController::class, 'index'])->name('membership');
Route::get('/membership/register', [MembershipController::class, 'create'])->name('membership.register');
Route::post('/membership/register', [MembershipController::class, 'store'])->name('membership.register.submit');
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/contact', [ContactPageController::class, 'index'])->name('contact');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/register-interest', [RegistrationController::class, 'store'])->name('register.interest');

Route::get('/privacy-policy', [LegalController::class, 'privacy'])->name('privacy');
Route::get('/terms', [LegalController::class, 'terms'])->name('terms');

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/payment/sandbox/{providerReference}', [PaymentController::class, 'sandboxShow'])->name('payment.sandbox.show');
Route::post('/payment/sandbox/{providerReference}/confirm', [PaymentController::class, 'sandboxConfirm'])->name('payment.sandbox.confirm');
Route::post('/payment/sandbox/{providerReference}/cancel', [PaymentController::class, 'sandboxCancel'])->name('payment.sandbox.cancel');
Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/guest/payment-success/{payment}', [PaymentController::class, 'guestSuccess'])->name('guest.payment.success');

Route::get('/member/login', [MemberAuthController::class, 'showLoginForm'])->name('member.login');
Route::post('/member/login', [MemberAuthController::class, 'login'])->name('member.login.submit');
Route::post('/member/logout', [MemberAuthController::class, 'logout'])->name('member.logout')->middleware('auth');

Route::middleware('member')->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

    Route::get('/library', [MemberBookController::class, 'library'])->name('library');

    Route::get('/membership', [MemberMembershipController::class, 'index'])->name('membership');

    Route::get('/profile', [MemberProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [MemberProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [MemberProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/payments', [MemberPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [MemberPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [MemberPaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [MemberPaymentController::class, 'show'])->name('payments.show');
});

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

Route::get('/admin/forgot-password', [AdminPasswordResetController::class, 'showForgotForm'])->name('admin.password.request');
Route::post('/admin/forgot-password', [AdminPasswordResetController::class, 'sendResetLink'])->name('admin.password.email');
Route::get('/admin/reset-password/{token}', [AdminPasswordResetController::class, 'showResetForm'])->name('admin.password.reset');
Route::post('/admin/reset-password', [AdminPasswordResetController::class, 'reset'])->name('admin.password.update');

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/programs', [AdminProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/create', [AdminProgramController::class, 'create'])->name('programs.create');
    Route::post('/programs', [AdminProgramController::class, 'store'])->name('programs.store');
    Route::get('/programs/{program}/edit', [AdminProgramController::class, 'edit'])->name('programs.edit');
    Route::put('/programs/{program}', [AdminProgramController::class, 'update'])->name('programs.update');
    Route::patch('/programs/{program}/toggle', [AdminProgramController::class, 'toggle'])->name('programs.toggle');
    Route::delete('/programs/{program}', [AdminProgramController::class, 'destroy'])->name('programs.destroy');

    Route::get('/convos', [AdminWeekendConvoController::class, 'index'])->name('convos.index');
    Route::get('/convos/create', [AdminWeekendConvoController::class, 'create'])->name('convos.create');
    Route::post('/convos', [AdminWeekendConvoController::class, 'store'])->name('convos.store');
    Route::get('/convos/{convo}/edit', [AdminWeekendConvoController::class, 'edit'])->name('convos.edit');
    Route::put('/convos/{convo}', [AdminWeekendConvoController::class, 'update'])->name('convos.update');
    Route::patch('/convos/{convo}/toggle', [AdminWeekendConvoController::class, 'toggle'])->name('convos.toggle');
    Route::delete('/convos/{convo}', [AdminWeekendConvoController::class, 'destroy'])->name('convos.destroy');

    Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
    Route::get('/books/purchases', [AdminBookController::class, 'purchases'])->name('books.purchases');
    Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
    Route::post('/books', [AdminBookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [AdminBookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [AdminBookController::class, 'destroy'])->name('books.destroy');

    Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
    Route::patch('/events/{event}/toggle', [AdminEventController::class, 'toggle'])->name('events.toggle');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');

    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{registration}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
    Route::patch('/registrations/{registration}/status', [AdminRegistrationController::class, 'updateStatus'])->name('registrations.status');
    Route::delete('/registrations/{registration}', [AdminRegistrationController::class, 'destroy'])->name('registrations.destroy');

    Route::get('/messages', [AdminContactMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [AdminContactMessageController::class, 'show'])->name('messages.show');
    Route::patch('/messages/{message}/read', [AdminContactMessageController::class, 'toggleRead'])->name('messages.read');
    Route::delete('/messages/{message}', [AdminContactMessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/settings/membership', [AdminMembershipSettingController::class, 'edit'])->name('membership_settings.edit');
        Route::put('/settings/membership', [AdminMembershipSettingController::class, 'update'])->name('membership_settings.update');

        Route::get('/members', [AdminMemberController::class, 'index'])->name('members.index');
        Route::get('/members/{member}', [AdminMemberController::class, 'show'])->name('members.show');
        Route::patch('/members/{member}/status', [AdminMemberController::class, 'updateStatus'])->name('members.status');
        Route::patch('/members/{member}/expired', [AdminMemberController::class, 'markExpired'])->name('members.expired');
        Route::post('/members/{member}/payments', [AdminMemberController::class, 'recordPayment'])->name('members.payments.store');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    });

    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [AdminProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::get('/profile/password', fn () => redirect()->route('admin.profile', '#security'))->name('profile.password');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit.index');

        Route::prefix('administrators')->name('administrators.')->group(function () {
            Route::get('/', [AdminAdministratorController::class, 'index'])->name('index');
            Route::get('/create', [AdminAdministratorController::class, 'create'])->name('create');
            Route::post('/', [AdminAdministratorController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminAdministratorController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminAdministratorController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminAdministratorController::class, 'update'])->name('update');
            Route::patch('/{user}/status', [AdminAdministratorController::class, 'toggleActive'])->name('status');
            Route::delete('/{user}', [AdminAdministratorController::class, 'destroy'])->name('destroy');
        });
    });
});
