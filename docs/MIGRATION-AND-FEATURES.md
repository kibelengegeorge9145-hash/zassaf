# Zassaf Elite Community — Migration & Features Documentation

> **Generated:** 2026-08-16  
> **Application:** Zassaf Elite Community (Laravel 13, PHP 8.5, MySQL)  
> **Current state:** 37 tests passing, 173 assertions

---

## Table of Contents

1. [Overview](#overview)
2. [Database Migration (SQLite to MySQL)](#database-migration)
3. [Guest Book Purchases Feature](#guest-book-purchases)
4. [Book Download Flow](#book-download-flow)
5. [Dynamic Logo System](#dynamic-logo-system)
6. [Logo Sizing](#logo-sizing)
7. [Database Schema Reference](#database-schema-reference)
8. [API & Route Reference](#api--route-reference)
9. [Environment Configuration](#environment-configuration)
10. [Testing Reference](#testing-reference)
11. [File Reference](#file-reference)

---

## 1. Overview

### What Was Done

| Area | Status | Description |
|------|--------|-------------|
| Database migration | Complete | SQLite → MySQL (zassaf database) |
| Book download flow | Complete | Admin PDF upload, protected download, library integration |
| Guest book purchases | Complete | Non-members can buy digital books via secure token |
| Dynamic logo system | Complete | Uploaded logo replaces hard-coded "Z" monogram |
| Logo sizing | Complete | Increased logo dimensions for navbar and footer |

### Test Suite

```
37 tests | 173 assertions | All passing
```

### Tech Stack

- **Framework:** Laravel 13
- **PHP:** 8.5
- **Database:** MySQL 8.4 (migrated from SQLite)
- **Frontend:** Tailwind CSS 4 + custom design system
- **Payments:** Sandbox provider (dev), configurable gateway
- **Cache:** Database driver
- **Session:** Database driver

---

## 2. Database Migration

### Migration: SQLite → MySQL

**Process followed:**

1. SQLite database backed up to `database/database.sqlite` (282 KB, preserved)
2. MySQL database `zassaf` created on `127.0.0.1:3306`
3. All 27 migrations applied to empty MySQL database
4. Data transferred with type conversion handling
5. Row counts verified for all tables

**Final .env configuration (active):**

```env
DB_CONNECTION=mysql
DB_DATABASE=zassaf
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=
```

**Note:** The `.env` file contains duplicate `DB_CONNECTION` / `DB_DATABASE` entries (lines 24–25 vs 26–29) — the second set (with leading whitespace) takes precedence. This is functional but should be cleaned up.

**Migrations (27 total):**

| # | Migration | Batch |
|---|-----------|-------|
| 1 | create_users_table | 1 |
| 2 | create_cache_table | 1 |
| 3 | create_jobs_table | 1 |
| 4 | create_programs_table | 1 |
| 5 | create_weekend_convos_table | 1 |
| 6 | create_books_table | 1 |
| 7 | create_events_table | 1 |
| 8 | create_registrations_table | 1 |
| 9 | create_contact_messages_table | 1 |
| 10 | create_settings_table | 1 |
| 11 | create_membership_plans_table | 1 |
| 12 | add_is_active_to_users_table | 1 |
| 13 | extend_users_for_admin_profiles | 1 |
| 14 | create_audit_logs_table | 1 |
| 15 | add_member_fields_to_users_table | 1 |
| 16 | extend_membership_plans_table | 1 |
| 17 | create_members_table | 1 |
| 18 | create_payments_table | 1 |
| 19 | create_sandbox_payments_table | 1 |
| 20 | add_transaction_reference_to_sandbox_payments_table | 1 |
| 21 | make_membership_number_nullable_on_members_table | 1 |
| 22 | alter_payments_for_book_purchases | 1 |
| 23 | create_book_purchases_table | 1 |
| 24 | add_file_path_to_books_table | 1 |
| 25 | add_guest_columns_to_payments_table | 1 |
| 26 | make_user_id_nullable_on_book_purchases_table | 1 |
| 27 | add_guest_columns_to_book_purchases_table | 1 |

---

## 3. Guest Book Purchases Feature

### Purpose

Allow non-members to purchase digital books without an account, alongside the existing member flow.

### Architecture

```
Member Flow:
  Login → Buy → Pay → Verify → BookPurchase → My Library → Read/Download

Guest Flow:
  Name/Email/Phone → Pay → Verify → Secure Token → Success Page → PDF Download
```

### Database Changes

#### payments table (guest columns)

```sql
-- Added by migration 2026_08_12_000001
ALTER TABLE payments ADD COLUMN customer_name VARCHAR(255) NULL;
ALTER TABLE payments ADD COLUMN customer_email VARCHAR(255) NULL;
ALTER TABLE payments ADD COLUMN customer_phone VARCHAR(40) NULL;
ALTER TABLE payments ADD COLUMN guest_download_token_hash VARCHAR(64) NULL;

CREATE INDEX idx_payments_customer_email ON payments(customer_email);
```

#### book_purchases table (guest columns + nullable user_id)

```sql
-- Added by migration 2026_08_12_000002
-- user_id made nullable for guest purchases
ALTER TABLE book_purchases DROP FOREIGN KEY book_purchases_user_id_foreign;
ALTER TABLE book_purchases MODIFY user_id BIGINT UNSIGNED NULL;
ALTER TABLE book_purchases ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Added by migration 2026_08_12_000003
ALTER TABLE book_purchases ADD COLUMN customer_name VARCHAR(255) NULL;
ALTER TABLE book_purchases ADD COLUMN customer_email VARCHAR(255) NULL;
ALTER TABLE book_purchases ADD COLUMN customer_phone VARCHAR(40) NULL;
ALTER TABLE book_purchases ADD COLUMN download_token_hash VARCHAR(64) NULL;
ALTER TABLE book_purchases ADD COLUMN download_token_expires_at TIMESTAMP NULL;

CREATE INDEX idx_book_purchases_customer_email ON book_purchases(customer_email);
CREATE INDEX idx_book_purchases_download_token_hash ON book_purchases(download_token_hash);
```

### Token Security

- **Raw token:** `bin2hex(random_bytes(32))` — 64-char hex string
- **Stored hash:** SHA-256 of raw token — only the hash is persisted
- **Expiry:** 7 days from payment verification
- **Storage:** Hash in `payments.guest_download_token_hash` and `book_purchases.download_token_hash`
- **Runtime:** Raw token in `session()` + `Cache` for display on success page only

### Key Classes

| Class | Responsibility |
|-------|----------------|
| `BookPurchaseService` | Purchase, access control, token generation/lookup |
| `PaymentTransactionService::createGuestBookPayment()` | Creates pending payment with guest fields + token hash |
| `PaymentVerificationService::settleBookPurchase()` | Settlement for both member and guest purchases |
| `BookController` (public) | `purchase()`, `guestCheckout()`, `guestDownload()` |
| `PaymentController` | `guestSuccess()`, `sendGuestBookConfirmation()`, `callback()` |
| `GuestBookPurchaseMail` | Mailable sent when mail is configured |

### Guest Purchase Service Methods

```php
// app/Services/BookPurchaseService.php

purchaseForGuest(Payment $payment, Book $book): BookPurchase
    // firstOrCreate by payment_id, copies guest fields + token hash from payment

generateDownloadToken(): array
    // Returns ['token' => hex-string, 'hash' => sha256-hash]

purchaseByDownloadToken(string $token): ?BookPurchase
    // Hash lookup → expiry check → PAID payment check → book exists

guestAlreadyPurchased(string $email, Book $book): bool
    // Checks for PAID guest purchase by email+book

pendingGuestPayment(string $email, Book $book): ?Payment
    // Returns pending/processing guest payment for reuse
```

### Routes Added

| Method | URI | Name | Auth |
|--------|-----|------|------|
| GET | `/books/{book:slug}/purchase` | `books.purchase` | Public |
| POST | `/books/{book:slug}/guest-checkout` | `books.guest.checkout` | Public |
| GET | `/guest-download/{token}` | `guest.download` | Public |
| GET | `/guest/payment-success/{payment}` | `guest.payment.success` | Public |

### Views Created

| View | Purpose |
|------|---------|
| `resources/views/pages/book-purchase.blade.php` | Member/guest chooser with guest form |
| `resources/views/payment/guest-success.blade.php` | Post-payment success with download link |
| `resources/views/emails/guest-book-purchase.blade.php` | Email template with download link |

### Language Keys Added (English + Swahili)

**`books.php`:**
- `purchase.*` — chooser page (heading, sub, member_title/desc/button, guest_title/desc, form fields)
- `guest_success.*` — success page (heading, paid, book, amount, reference, download notes)
- `emails.*` — email template (subject, heading, greeting, download button, expiry note)

**`admin.php`:**
- `books.purchases.guest` — "Guest" label in admin purchases table

### Admin Changes

- `resources/views/admin/books/purchases.blade.php` — Customer column shows guest name/email/phone when `user_id` is null
- Admin settings preview (`admin/settings/edit.blade.php`) — Guarded with `Storage::disk('public')->exists()` check

### Duplicate Purchase Prevention

```
Guest tries to buy same book twice →
  BookPurchaseService::guestAlreadyPurchased() returns true →
    PaymentTransactionService throws PaymentException("You already own this book.") →
      Controller catches, redirects back with error flash
```

### Callback Flow (Guest)

```
1. POST /payment/callback?transaction_reference=ZP-...&provider_reference=SB-...
2. PaymentVerificationService verifies via sandbox provider
3. Payment marked PAID
4. settleBookPurchase() → purchaseForGuest() creates BookPurchase record
5. Callback controller checks isGuestBookPayment()
6. sendGuestBookConfirmation() — sends email if mail configured
7. Redirect to guest.payment.success with session flash
```

---

## 4. Book Download Flow

### Migration: `add_file_path_to_books_table`

```sql
ALTER TABLE books ADD COLUMN file_path VARCHAR(255) NULL AFTER cover_path;
```

### PDF Upload (Admin)

- **Storage:** `storage/app/private/books/` (private disk)
- **File naming:** `books/ebook-{uniqid}.pdf`
- **Validation:** Magic bytes `%PDF-` + `mimes:pdf` + max 10MB
- **Safe replacement:** Store new file first, then delete old file

### Download Route (Members)

```
GET /books/{book:slug}/download → Member\BookController@download
Middleware: member
```

**Access check:**
1. `BookPurchaseService::hasAccess()` — book purchase tied to PAID payment
2. `Storage::disk('local')->exists($book->file_path)` — file exists
3. Returns `Storage::disk('local')->download()` with `{title}.pdf` filename

### Download Route (Guests)

```
GET /guest-download/{token} → BookController@guestDownload
Middleware: none (public, token-based)
```

**Access check:**
1. `BookPurchaseService::purchaseByDownloadToken($token)` — hash match + expiry + PAID
2. `Storage::disk('local')->exists($book->file_path)` — file exists
3. Returns `Storage::disk('local')->download()` with `{title}.pdf` filename

---

## 5. Dynamic Logo System

### Purpose

Replace hard-coded "Z" monogram with uploaded admin logo throughout public website.

### Flow

```
Admin → Settings → Upload PNG/JPG/SVG/WebP
       ↓
Storage → storage/app/public/settings/{filename}.ext
       ↓
Setting::set('logo_path', 'settings/{filename}.ext')
       ↓
Navbar + Footer → conditional display
```

### Conditional Logic (Blade)

Both `navbar.blade.php` and `footer.blade.php` use identical logic:

```php
@php
    $brandLogoPath = setting('logo_path');
    $brandLogoUrl = $brandLogoPath && Storage::disk('public')->exists($brandLogoPath)
        ? asset('storage/'.$brandLogoPath)
        : null;
@endphp

@if ($brandLogoUrl)
    <img src="{{ $brandLogoUrl }}"
         alt="{{ setting('org_name', 'Zassaf Elite Community') }}"
         class="brand-logo">
@else
    <span class="brand-mark" aria-hidden="true">Z</span>
@endif
```

### Fallback Behavior

| Condition | Display |
|-----------|---------|
| `logo_path` set AND file exists | Uploaded logo image |
| `logo_path` empty or null | Z monogram (`brand-mark`) |
| `logo_path` set but file missing | Z monogram (no broken image) |

### Admin Settings Preview

```php
@if ($logoPreviewPath && Storage::disk('public')->exists($logoPreviewPath))
    <div class="logo-preview">
        <img src="{{ asset('storage/' . $logoPreviewPath) }}" alt="...">
        <label class="checkbox-label">
            <input type="checkbox" name="remove_logo" value="1">
            <span>Remove logo — This will restore the default Z monogram.</span>
        </label>
    </div>
@endif
```

### Key Detail

Uses `asset('storage/' . $path)` (not `Storage::url()`) for host-relative URLs that work across different environments.

---

## 6. Logo Sizing

### CSS Rules

**File:** `resources/css/app.css` + `public/css/app.css` (kept in sync)

#### Desktop (navbar)

```css
.brand-logo {
    display: block;
    width: auto;
    height: 60px;
    max-width: 220px;
    object-fit: contain;
    flex-shrink: 0;
}
```

#### Desktop (footer)

```css
.footer-brand-logo .brand-logo {
    height: 65px;
    max-width: 240px;
}
```

#### Mobile (≤ 640px)

```css
.brand-logo {
    height: 50px;
    max-width: 180px;
}

.footer-brand-logo .brand-logo {
    height: 55px;
    max-width: 200px;
}
```

---

## 7. Database Schema Reference

### Tables with Row Counts (at migration time)

| Table | Rows | Description |
|-------|------|-------------|
| users | 3 | Admin + member accounts |
| members | 1 | Active membership record |
| books | 3 | Digital book catalog |
| payments | 4 | Payment records (membership + book) |
| book_purchases | 1 | Book ownership records |
| programs | 4 | Community programs |
| events | 1 | Events |
| weekend_convos | 1 | Weekend conversations |
| registrations | 2 | Interest registrations |
| contact_messages | 1 | Contact form submissions |
| settings | 19 | Application settings (key-value) |
| membership_plans | 2 | Membership plan definitions |
| sandbox_payments | 2 | Sandbox payment ledger |
| audit_logs | 14 | Admin audit trail |
| sessions | 20 | User sessions |
| cache / cache_locks | 2 / 0 | Application cache |
| jobs / job_batches / failed_jobs | 0 each | Queue infrastructure |

### Key Relationships

```
users ──< members        (has one)
users ──< payments       (user_id, nullable for guests)
users ──< book_purchases (user_id, nullable for guests)
books ──< book_purchases (has many)
payments ──< book_purchases (payment_id)
books ──< payments       (book_id, for book purchases)
members ──< payments     (member_id, for membership payments)
programs ──< registrations (program_id, nullable)
events ──< registrations (event_id, nullable)
```

### Important Constraints

- `payments.transaction_reference` — unique
- `payments.provider_reference` — unique, nullable
- `book_purchases(user_id, book_id)` — unique (only when user_id NOT NULL)
- `book_purchases(payment_id)` — unique (prevents duplicate settlement)
- `sandbox_payments.provider_reference` — unique
- `sandbox_payments.transaction_reference` — unique

---

## 8. API & Route Reference

### Public Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/` | `home` | Homepage |
| GET | `/about` | `about` | About page |
| GET | `/programs` | `programs` | Programs listing |
| GET | `/weekend-convo` | `weekend-convo` | Weekend conversations |
| GET | `/books` | `books` | Book catalog |
| GET | `/books/{book:slug}` | `books.show` | Book detail page |
| GET | `/books/{book:slug}/purchase` | `books.purchase` | Purchase chooser (member/guest) |
| POST | `/books/{book:slug}/guest-checkout` | `books.guest.checkout` | Guest payment initiation |
| GET | `/guest-download/{token}` | `guest.download` | Guest PDF download |
| GET | `/guest/payment-success/{payment}` | `guest.payment.success` | Guest payment success page |
| GET | `/community` | `community` | Community page |
| GET | `/membership` | `membership` | Membership info |
| GET | `/membership/register` | `membership.register` | Registration form |
| POST | `/membership/register` | `membership.register.submit` | Submit registration |
| GET | `/events` | `events` | Events listing |
| GET | `/contact` | `contact` | Contact page |
| POST | `/contact` | `contact.store` | Submit contact form |
| GET | `/privacy-policy` | `privacy` | Privacy policy |
| GET | `/terms` | `terms` | Terms of service |
| GET | `/locale/{locale}` | `locale.switch` | Language switcher |

### Payment Routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/payment/sandbox/{providerReference}` | `payment.sandbox.show` | Sandbox payment page |
| POST | `/payment/sandbox/{providerReference}/confirm` | `payment.sandbox.confirm` | Sandbox confirm |
| POST | `/payment/sandbox/{providerReference}/cancel` | `payment.sandbox.cancel` | Sandbox cancel |
| GET/POST | `/payment/callback` | `payment.callback` | Payment callback |
| POST | `/payment/webhook` | `payment.webhook` | Webhook endpoint |

### Member Routes (middleware: `member`)

| Method | URI | Name |
|--------|-----|------|
| GET | `/member/dashboard` | `member.dashboard` |
| GET | `/member/library` | `member.library` |
| GET | `/member/membership` | `member.membership` |
| GET | `/member/profile` | `member.profile` |
| PUT | `/member/profile` | `member.profile.update` |
| PUT | `/member/profile/password` | `member.profile.password` |
| GET | `/member/payments` | `member.payments.index` |
| GET | `/member/payments/create` | `member.payments.create` |
| POST | `/member/payments` | `member.payments.store` |
| GET | `/books/{book:slug}/checkout` | `books.checkout` |
| POST | `/books/{book:slug}/buy` | `books.buy` |
| GET | `/books/{book:slug}/read` | `books.read` |
| GET | `/books/{book:slug}/download` | `books.download` |

### Admin Routes (middleware: `admin`)

| Method | URI | Name |
|--------|-----|------|
| GET | `/admin` | `admin.dashboard` |
| GET/PUT | `/admin/settings` | `admin.settings.{edit,update}` |
| GET/POST | `/admin/books` | `admin.books.{index,store}` |
| GET | `/admin/books/purchases` | `admin.books.purchases` |
| GET | `/admin/payments` | `admin.payments.index` |
| ... | *(full admin CRUD)* | |

---

## 9. Environment Configuration

### Current `.env` (relevant entries)

```env
APP_NAME="Zassaf Elite Community"
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=zassaf
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=log

PAYMENT_PROVIDER=sandbox
PAYMENT_SANDBOX=true

ADMIN_EMAIL="admin@zassaf.com"
ADMIN_PASSWORD="password"
```

### Test Environment (`phpunit.xml`)

```xml
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_STORE=array
SESSION_DRIVER=array
MAIL_MAILER=array
```

### PHP CLI Requirements

```bash
export PHP_INI_SCAN_DIR="/etc/php/8.5/cli/conf.d:/home/kali/Documents/Default Project/zassaf/.php/sqlite/conf"
```

Required for `php artisan` commands and tests. The sqlite3/pdo_sqlite warnings from PHP are benign.

---

## 10. Testing Reference

### Running Tests

```bash
# Full test suite
export PHP_INI_SCAN_DIR="/etc/php/8.5/cli/conf.d:/home/kali/Documents/Default Project/zassaf/.php/sqlite/conf"
php artisan test

# Specific test class
php artisan test --filter=BookPurchaseTest
php artisan test --filter=GuestBookPurchaseTest
php artisan test --filter=BrandLogoTest

# Current status: 37 tests, 173 assertions, all passing
```

### Test Files

| File | Tests | Focus |
|------|-------|-------|
| `tests/Feature/AdminAuthTest.php` | 6 | Admin authentication |
| `tests/Feature/ExampleTest.php` | 1 | Basic app boot |
| `tests/Feature/BookPurchaseTest.php` | 20 | Member book purchase + download flow |
| `tests/Feature/GuestBookPurchaseTest.php` | 12 | Guest book purchase + download flow |
| `tests/Feature/BrandLogoTest.php` | 5 | Logo display + fallback logic |

### Key Test Helpers

**`BookPurchaseTest`:**

```php
SAMPLE_PDF // Valid minimal PDF content string
makeBook($content) // Creates book with real PDF in Storage::fake('local')
makeBookWithoutFile() // Book without file_path (preorder)
makeMember($email) // Creates active member user
markBookPaid($user, $book) // Directly creates PAID payment + BookPurchase
```

**`GuestBookPurchaseTest`:**

```php
SAMPLE_PDF_A // Book A content (distinguishable)
SAMPLE_PDF_B // Book B content (distinguishable)
checkoutAsGuest($book, $overrides) // POST guest checkout → returns [response, providerReference, ledger, payment, token]
confirmAndSettle($guestCheckout) // sandbox confirm + callback → marks payment PAID
```

### Test Gotchas

| Issue | Resolution |
|-------|-----------|
| `UploadedFile::fake()->create()` returns empty file in Laravel 13 | Use `createWithContent()` for real PDF bytes |
| `StreamedResponse` cannot use `assertContent()` | Use `streamedContent()` for download assertions |
| `Storage::fake('local')` needed in tests | Ensures files don't touch real disk |
| SQLite nullable column migration | Tested — `2026_08_12_000002` runs cleanly on SQLite |
| Cache store in tests | `array` (in-memory, per test) |

---

## 11. File Reference

### Migrations

| File | Purpose |
|------|---------|
| `database/migrations/2026_08_11_000010_add_file_path_to_books_table.php` | Adds `file_path` to books |
| `database/migrations/2026_08_12_000001_add_guest_columns_to_payments_table.php` | Guest columns on payments |
| `database/migrations/2026_08_12_000002_make_user_id_nullable_on_book_purchases_table.php` | Nullable user_id on book_purchases |
| `database/migrations/2026_08_12_000003_add_guest_columns_to_book_purchases_table.php` | Guest columns + token on book_purchases |

### Models Modified

| File | Changes |
|------|---------|
| `app/Models/Payment.php` | Guest fields fillable, `isGuestBookPayment()` method |
| `app/Models/BookPurchase.php` | Guest fields fillable, `download_token_expires_at` cast, `isGuestPurchase()` method |
| `app/Models/Book.php` | `file_path` fillable (via existing migration) |

### Services

| File | Purpose |
|------|---------|
| `app/Services/BookPurchaseService.php` | Member + guest purchase, token management, access control |
| `app/Services/Payments/PaymentTransactionService.php` | `createGuestBookPayment()` added |
| `app/Services/Payments/PaymentVerificationService.php` | `settleBookPurchase()` handles guests |

### Controllers

| File | Guest Methods Added |
|------|-------------------|
| `app/Http/Controllers/BookController.php` | `purchase()`, `guestCheckout()`, `guestDownload()` |
| `app/Http/Controllers/PaymentController.php` | `guestSuccess()`, `sendGuestBookConfirmation()`, `mailConfigured()` |
| `app/Http/Controllers/Admin/SettingController.php` | No changes (logo upload was pre-existing) |

### Mail

| File | Purpose |
|------|---------|
| `app/Mail/GuestBookPurchaseMail.php` | Mailable for guest purchase confirmation |

### Views

| File | Purpose |
|------|---------|
| `resources/views/pages/book-purchase.blade.php` | Purchase chooser (member/guest) |
| `resources/views/payment/guest-success.blade.php` | Guest payment success page |
| `resources/views/emails/guest-book-purchase.blade.php` | Guest purchase email template |
| `resources/views/components/navbar.blade.php` | Updated — dynamic logo |
| `resources/views/components/footer.blade.php` | Updated — dynamic logo |
| `resources/views/admin/settings/edit.blade.php` | Updated — robust logo preview |
| `resources/views/admin/books/purchases.blade.php` | Updated — guest customer display |
| `resources/views/pages/books-show.blade.php` | Updated — Buy Now → chooser route |

### CSS

| File | Updates |
|------|---------|
| `resources/css/app.css` | `.brand-logo`, `.footer-brand-logo .brand-logo`, `.checkout-options`, `.checkout-option`, `.checkout-guest-form` |
| `public/css/app.css` | Same as above (kept in sync) |

### Language Files

| File | Keys Added |
|------|-----------|
| `lang/en/books.php` | `purchase.*`, `guest_success.*`, `emails.*` |
| `lang/sw/books.php` | `purchase.*`, `guest_success.*`, `emails.*` |
| `lang/en/admin.php` | `books.purchases.guest` |
| `lang/sw/admin.php` | `books.purchases.guest` |

### Routes

| File | Changes |
|------|---------|
| `routes/web.php` | 4 new routes: `books.purchase`, `books.guest.checkout`, `guest.download`, `guest.payment.success` |

### Storage

| Path | Purpose |
|------|---------|
| `storage/app/private/books/` | PDF files (private, not web-accessible) |
| `storage/app/public/settings/` | Uploaded organization logos (public) |
| `public/storage/` | Symlink → `storage/app/public/` |

### Tests

| File | Tests |
|------|-------|
| `tests/Feature/BookPurchaseTest.php` | 20 tests — member purchase + download + PDF validation |
| `tests/Feature/GuestBookPurchaseTest.php` | 12 tests — guest purchase + download + security |
| `tests/Feature/BrandLogoTest.php` | 5 tests — logo display + fallback + admin preview |

---

## Appendix: Quick Commands

```bash
# Clear all caches
php artisan optimize:clear

# Check migration status
php artisan migrate:status

# Run full test suite
export PHP_INI_SCAN_DIR="/etc/php/8.5/cli/conf.d:/home/kali/Documents/Default Project/zassaf/.php/sqlite/conf"
php artisan test

# Start dev server
php artisan serve

# Check DB config
php artisan config:show database.connections.mysql

# Verify logo path
php artisan tinker --execute='echo App\Models\Setting::value("logo_path");'
```
