<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Payment;
use App\Models\SandboxPayment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuestBookPurchaseTest extends TestCase
{
    use RefreshDatabase;

    private const SAMPLE_PDF_A = "%PDF-1.4\n1 0 obj\n<</Type /Catalog>>\nendobj\nxref\n0 1\n0000000000 65535 f \ntrailer\n<</Root 1 0 R>>\n%%EOF\nBOOK-A\n";
    private const SAMPLE_PDF_B = "%PDF-1.4\n1 0 obj\n<</Type /Catalog>>\nendobj\nxref\n0 1\n0000000000 65535 f \ntrailer\n<</Root 1 0 R>>\n%%EOF\nBOOK-B\n";

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        Setting::set('membership_payment_enabled', '1');

        Storage::fake('local');
    }

    private function makeBook(string $content = self::SAMPLE_PDF_A): Book
    {
        $path = 'books/ebook-'.uniqid().'.pdf';

        Storage::disk('local')->put($path, $content);

        return Book::create([
            'slug' => 'test-ebook-'.uniqid(),
            'title_en' => 'Test Ebook',
            'title_sw' => null,
            'description_en' => 'A test digital book.',
            'description_sw' => null,
            'author' => 'Test Author',
            'file_path' => $path,
            'status' => Book::STATUS_PUBLISHED,
            'price' => 20000,
            'currency' => 'TZS',
        ]);
    }

    private function makeAdmin(): User
    {
        return User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first();
    }

    private function checkoutAsGuest(Book $book, array $overrides = []): array
    {
        $payload = array_merge([
            'customer_name' => 'Guest Customer',
            'customer_email' => 'guest@example.test',
            'customer_phone' => '+255700000000',
            'payment_method' => 'mobile_money',
        ], $overrides);

        $response = $this->post(route('books.guest.checkout', $book), $payload);

        preg_match('#/payment/sandbox/([^/]+)#', $response->headers->get('Location'), $matches);
        $providerReference = $matches[1] ?? null;

        $ledger = $providerReference
            ? SandboxPayment::where('provider_reference', $providerReference)->first()
            : null;

        $payment = $ledger
            ? Payment::where('transaction_reference', $ledger->transaction_reference)->first()
            : null;

        $token = $payment ? Cache::get("guest_download_token_{$payment->id}") : null;

        return compact('response', 'providerReference', 'ledger', 'payment', 'token');
    }

    private function confirmAndSettle(array $guestCheckout, array $overrides = []): void
    {
        $this->post(route('payment.sandbox.confirm', $guestCheckout['providerReference']));

        $this->get(route('payment.callback', array_merge([
            'transaction_reference' => $guestCheckout['payment']->transaction_reference,
            'provider_reference' => $guestCheckout['providerReference'],
        ], $overrides)));
    }

    public function test_guest_can_purchase_and_download_a_book_end_to_end(): void
    {
        $book = $this->makeBook();

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertSee('Buy Now')
            ->assertSee(route('books.purchase', $book));

        $this->get(route('books.purchase', $book))
            ->assertOk()
            ->assertSee($book->title_en)
            ->assertSee(route('books.guest.checkout', $book))
            ->assertSee('Continue as Member');

        $guestCheckout = $this->checkoutAsGuest($book);

        $guestCheckout['response']->assertRedirect();
        $this->assertNotNull($guestCheckout['providerReference']);

        $payment = $guestCheckout['payment'];
        $token = $guestCheckout['token'];

        $this->assertNotNull($payment);
        $this->assertTrue($payment->isPending());
        $this->assertNull($payment->user_id);
        $this->assertEquals('guest@example.test', $payment->customer_email);
        $this->assertEquals('Guest Customer', $payment->customer_name);
        $this->assertEquals('+255700000000', $payment->customer_phone);
        $this->assertEquals($book->price, (float) $payment->amount);
        $this->assertEquals(Payment::TYPE_BOOK, $payment->payment_type);
        $this->assertNotEmpty($payment->guest_download_token_hash);
        $this->assertNotNull($token);
        $this->assertNotSame($token, $payment->guest_download_token_hash);
        $this->assertSame(hash('sha256', $token), $payment->guest_download_token_hash);

        $this->get(route('payment.sandbox.show', $guestCheckout['providerReference']))->assertOk();

        $this->confirmAndSettle($guestCheckout);

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => Payment::STATUS_PAID]);
        $this->assertDatabaseHas('book_purchases', [
            'user_id' => null,
            'book_id' => $book->id,
            'payment_id' => $payment->id,
            'customer_email' => 'guest@example.test',
            'download_token_hash' => $payment->guest_download_token_hash,
        ]);

        $this->get(route('guest.payment.success', $payment))
            ->assertOk()
            ->assertSee('Paid')
            ->assertSee($book->title_en)
            ->assertSee(route('guest.download', $token));

        $response = $this->get(route('guest.download', $token));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertSame(self::SAMPLE_PDF_A, $response->streamedContent());
    }

    public function test_guest_cannot_download_before_payment_is_verified(): void
    {
        $book = $this->makeBook();

        $guestCheckout = $this->checkoutAsGuest($book);

        $this->assertTrue($guestCheckout['payment']->isPending());

        $this->get(route('guest.download', $guestCheckout['token']))->assertForbidden();

        $this->assertSame(0, BookPurchase::where('book_id', $book->id)->count());
    }

    public function test_guest_cannot_download_when_payment_failed(): void
    {
        $book = $this->makeBook();

        $guestCheckout = $this->checkoutAsGuest($book);

        $guestCheckout['ledger']->update(['status' => SandboxPayment::STATUS_FAILED]);

        $this->get(route('payment.callback', [
            'transaction_reference' => $guestCheckout['payment']->transaction_reference,
            'provider_reference' => $guestCheckout['providerReference'],
        ]))->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('payments', [
            'id' => $guestCheckout['payment']->id,
            'status' => Payment::STATUS_FAILED,
        ]);

        $this->get(route('guest.download', $guestCheckout['token']))->assertForbidden();

        $this->assertSame(0, BookPurchase::where('book_id', $book->id)->count());
    }

    public function test_guest_cannot_buy_the_same_book_twice(): void
    {
        $book = $this->makeBook();

        $first = $this->checkoutAsGuest($book);
        $this->confirmAndSettle($first);

        $this->post(route('books.guest.checkout', $book), [
            'customer_name' => 'Guest Customer',
            'customer_email' => 'guest@example.test',
            'customer_phone' => '+255700000000',
            'payment_method' => 'mobile_money',
        ])->assertSessionHas('error');

        $this->assertSame(
            1,
            Payment::where('payment_type', Payment::TYPE_BOOK)
                ->whereNull('user_id')
                ->where('customer_email', 'guest@example.test')
                ->count()
        );

        $this->assertSame(1, BookPurchase::where('book_id', $book->id)->count());
    }

    public function test_guest_download_with_random_token_is_denied(): void
    {
        $book = $this->makeBook();

        $guestCheckout = $this->checkoutAsGuest($book);
        $this->confirmAndSettle($guestCheckout);

        $this->get(route('guest.download', bin2hex(random_bytes(32))))->assertForbidden();
    }

    public function test_guest_token_cannot_download_a_different_book(): void
    {
        $bookA = $this->makeBook(self::SAMPLE_PDF_A);
        $bookB = $this->makeBook(self::SAMPLE_PDF_B);

        $checkoutA = $this->checkoutAsGuest($bookA);
        $this->confirmAndSettle($checkoutA);

        $checkoutB = $this->checkoutAsGuest($bookB, [
            'customer_email' => 'other@example.test',
            'customer_name' => 'Other Guest',
        ]);
        $this->confirmAndSettle($checkoutB);

        $this->assertNotSame($checkoutA['token'], $checkoutB['token']);

        $downloadA = $this->get(route('guest.download', $checkoutA['token']));
        $downloadA->assertOk();
        $this->assertSame(self::SAMPLE_PDF_A, $downloadA->streamedContent());

        $downloadB = $this->get(route('guest.download', $checkoutB['token']));
        $downloadB->assertOk();
        $this->assertSame(self::SAMPLE_PDF_B, $downloadB->streamedContent());
    }

    public function test_guest_cannot_use_member_only_checkout_read_or_download_routes(): void
    {
        $book = $this->makeBook();

        $this->get(route('books.checkout', $book))->assertRedirect(route('member.login'));
        $this->get(route('books.read', $book))->assertRedirect(route('member.login'));
        $this->get(route('books.download', $book))->assertRedirect(route('member.login'));
    }

    public function test_repeated_callback_does_not_duplicate_a_guest_purchase(): void
    {
        $book = $this->makeBook();

        $guestCheckout = $this->checkoutAsGuest($book);

        $this->confirmAndSettle($guestCheckout);
        $this->confirmAndSettle($guestCheckout);

        $this->assertSame(
            1,
            BookPurchase::where('payment_id', $guestCheckout['payment']->id)->count()
        );
        $this->assertSame(1, BookPurchase::where('book_id', $book->id)->count());
        $this->assertSame(1, Payment::where('id', $guestCheckout['payment']->id)->count());
    }

    public function test_admin_sees_guest_purchases(): void
    {
        $book = $this->makeBook();

        $guestCheckout = $this->checkoutAsGuest($book);
        $this->confirmAndSettle($guestCheckout);

        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get(route('admin.books.purchases'))
            ->assertOk()
            ->assertSee('Guest Customer')
            ->assertSee('guest@example.test')
            ->assertSee('+255700000000');
    }

    public function test_guest_checkout_rejects_an_unsupported_payment_method(): void
    {
        $book = $this->makeBook();

        $this->from(route('books.purchase', $book))->post(route('books.guest.checkout', $book), [
            'customer_name' => 'Guest Customer',
            'customer_email' => 'guest@example.test',
            'customer_phone' => '+255700000000',
            'payment_method' => 'crypto',
        ])->assertRedirect(route('books.purchase', $book))->assertSessionHas('error');

        $this->assertSame(0, Payment::where('payment_type', Payment::TYPE_BOOK)->count());
    }

    public function test_guest_checkout_is_blocked_when_payments_are_disabled(): void
    {
        $book = $this->makeBook();

        Setting::set('membership_payment_enabled', '0');

        $this->from(route('books.purchase', $book))->post(route('books.guest.checkout', $book), [
            'customer_name' => 'Guest Customer',
            'customer_email' => 'guest@example.test',
            'customer_phone' => '+255700000000',
            'payment_method' => 'mobile_money',
        ])->assertRedirect(route('books.purchase', $book))->assertSessionHas('error');

        $this->assertSame(0, Payment::where('payment_type', Payment::TYPE_BOOK)->count());
    }

    public function test_purchase_page_is_hidden_for_a_non_purchasable_book(): void
    {
        $book = $this->makeBook();

        $book->update(['status' => Book::STATUS_PREORDER]);

        $this->get(route('books.purchase', $book))->assertNotFound();

        $this->from(route('books.show', $book))->post(route('books.guest.checkout', $book), [
            'customer_name' => 'Guest Customer',
            'customer_email' => 'guest@example.test',
            'customer_phone' => '+255700000000',
            'payment_method' => 'mobile_money',
        ])->assertSessionHas('error');

        $this->assertSame(0, Payment::where('payment_type', Payment::TYPE_BOOK)->count());
    }
}
