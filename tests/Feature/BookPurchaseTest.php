<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Member;
use App\Models\Payment;
use App\Models\SandboxPayment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookPurchaseTest extends TestCase
{
    use RefreshDatabase;

    private const SAMPLE_PDF = "%PDF-1.4\n1 0 obj\n<</Type /Catalog>>\nendobj\nxref\n0 1\n0000000000 65535 f \ntrailer\n<</Root 1 0 R>>\n%%EOF\n";

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        Setting::set('membership_payment_enabled', '1');

        Storage::fake('local');
    }

    private function makeMember(string $email = 'buyer@example.test', string $name = 'Book Buyer'): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
        ]);

        $user->member()->create([
            'plan_id' => null,
            'membership_number' => 'ZE-'.strtoupper(uniqid()),
            'status' => Member::STATUS_ACTIVE,
            'joined_at' => now(),
            'expires_at' => now()->addMonth(),
        ]);

        return $user;
    }

    private function makeBook(): Book
    {
        $path = 'books/ebook-'.uniqid().'.pdf';

        Storage::disk('local')->put($path, self::SAMPLE_PDF);

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

    private function makeBookWithoutFile(): Book
    {
        return Book::create([
            'slug' => 'no-file-'.uniqid(),
            'title_en' => 'No File Ebook',
            'title_sw' => null,
            'description_en' => 'A test digital book without a PDF.',
            'description_sw' => null,
            'author' => 'Test Author',
            'file_path' => null,
            'status' => Book::STATUS_PREORDER,
            'price' => 20000,
            'currency' => 'TZS',
        ]);
    }

    private function markBookPaid(User $user, Book $book): Payment
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'transaction_reference' => 'ZP-TEST-'.strtoupper(uniqid()),
            'amount' => $book->price,
            'payment_type' => Payment::TYPE_BOOK,
            'payment_method' => 'mobile_money',
            'status' => Payment::STATUS_PAID,
            'paid_at' => now(),
        ]);

        \App\Models\BookPurchase::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'payment_id' => $payment->id,
            'purchased_at' => now(),
        ]);

        return $payment;
    }

    public function test_public_book_detail_page_is_accessible(): void
    {
        $book = $this->makeBook();

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertSee($book->title_en)
            ->assertSee('Buy Now')
            ->assertSee(route('books.purchase', $book));
    }

    public function test_guest_cannot_buy_or_access_a_book(): void
    {
        $book = $this->makeBook();

        $this->post(route('books.buy', $book), ['payment_method' => 'mobile_money'])
            ->assertRedirect(route('member.login'));

        $this->get(route('books.read', $book))->assertRedirect(route('member.login'));
        $this->get(route('books.download', $book))->assertRedirect(route('member.login'));
    }

    public function test_member_can_purchase_a_book_end_to_end(): void
    {
        $user = $this->makeMember();
        $book = $this->makeBook();

        $response = $this->actingAs($user)->post(route('books.buy', $book), [
            'payment_method' => 'mobile_money',
        ]);

        $response->assertRedirect();

        preg_match('#/payment/sandbox/([^/]+)#', $response->headers->get('Location'), $matches);
        $this->assertNotEmpty($matches);
        $providerReference = $matches[1];

        $ledger = SandboxPayment::where('provider_reference', $providerReference)->first();
        $this->assertNotNull($ledger);

        $payment = Payment::where('transaction_reference', $ledger->transaction_reference)->first();
        $this->assertNotNull($payment);
        $this->assertTrue($payment->isPending());
        $this->assertEquals($book->price, (float) $payment->amount);
        $this->assertEquals(Payment::TYPE_BOOK, $payment->payment_type);

        $this->actingAs($user)->get(route('payment.sandbox.show', $providerReference))->assertOk();
        $this->actingAs($user)->post("/payment/sandbox/{$providerReference}/confirm");

        $this->actingAs($user)->get(route('payment.callback', [
            'transaction_reference' => $payment->transaction_reference,
            'provider_reference' => $providerReference,
        ]))->assertRedirect(route('member.library'));

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => Payment::STATUS_PAID]);
        $this->assertDatabaseHas('book_purchases', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'payment_id' => $payment->id,
        ]);

        $this->actingAs($user)->get(route('member.library'))
            ->assertOk()
            ->assertSee($book->title_en)
            ->assertSee(route('books.download', $book));

        $this->actingAs($user)->get(route('books.read', $book))
            ->assertOk()
            ->assertStreamed();

        $this->actingAs($user)->get(route('books.download', $book))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_duplicate_purchase_is_prevented(): void
    {
        $user = $this->makeMember();
        $book = $this->makeBook();

        $response = $this->actingAs($user)->post(route('books.buy', $book), [
            'payment_method' => 'mobile_money',
        ]);
        $response->assertRedirect();

        preg_match('#/payment/sandbox/([^/]+)#', $response->headers->get('Location'), $matches);
        $providerReference = $matches[1];
        $ledger = SandboxPayment::where('provider_reference', $providerReference)->first();
        $payment = Payment::where('transaction_reference', $ledger->transaction_reference)->first();

        $this->actingAs($user)->post("/payment/sandbox/{$providerReference}/confirm");
        $this->actingAs($user)->get(route('payment.callback', [
            'transaction_reference' => $payment->transaction_reference,
            'provider_reference' => $providerReference,
        ]));

        $this->actingAs($user)->post(route('books.buy', $book), [
            'payment_method' => 'mobile_money',
        ])->assertSessionHas('error');

        $this->assertSame(1, Payment::where('payment_type', Payment::TYPE_BOOK)->count());
        $this->assertSame(1, \App\Models\BookPurchase::where('book_id', $book->id)->count());
    }

    public function test_non_owner_cannot_read_or_download(): void
    {
        $owner = $this->makeMember('owner@example.test', 'Owner');
        $other = $this->makeMember('other@example.test', 'Other');
        $book = $this->makeBook();

        $response = $this->actingAs($owner)->post(route('books.buy', $book), [
            'payment_method' => 'mobile_money',
        ]);
        preg_match('#/payment/sandbox/([^/]+)#', $response->headers->get('Location'), $matches);
        $providerReference = $matches[1];
        $ledger = SandboxPayment::where('provider_reference', $providerReference)->first();
        $payment = Payment::where('transaction_reference', $ledger->transaction_reference)->first();

        $this->actingAs($owner)->post("/payment/sandbox/{$providerReference}/confirm");
        $this->actingAs($owner)->get(route('payment.callback', [
            'transaction_reference' => $payment->transaction_reference,
            'provider_reference' => $providerReference,
        ]));

        $this->actingAs($other)->get(route('books.read', $book))->assertForbidden();
        $this->actingAs($other)->get(route('books.download', $book))->assertForbidden();
    }

    public function test_admin_can_upload_a_pdf_for_a_book(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first();

        $this->actingAs($admin)->post(route('admin.books.store'), [
            'title_en' => 'Admin Ebook',
            'description_en' => 'Description.',
            'author' => 'Author Name',
            'status' => Book::STATUS_PUBLISHED,
            'price' => '15000',
            'currency' => 'TZS',
            'pdf' => UploadedFile::fake()->createWithContent('ebook.pdf', self::SAMPLE_PDF),
        ])->assertRedirect(route('admin.books.index'));

        $book = Book::where('title_en', 'Admin Ebook')->first();
        $this->assertNotNull($book);
        $this->assertTrue($book->hasFile());
    }

    public function test_admin_pdf_is_stored_in_private_storage(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first();

        $this->actingAs($admin)->post(route('admin.books.store'), [
            'title_en' => 'Private Ebook',
            'description_en' => 'Description.',
            'author' => 'Author Name',
            'status' => Book::STATUS_PUBLISHED,
            'price' => '15000',
            'currency' => 'TZS',
            'pdf' => UploadedFile::fake()->createWithContent('ebook.pdf', self::SAMPLE_PDF),
        ])->assertRedirect(route('admin.books.index'));

        $book = Book::where('title_en', 'Private Ebook')->first();

        Storage::disk('local')->assertExists($book->file_path);
        $this->assertStringStartsWith('books/', $book->file_path);
        Storage::disk('public')->assertMissing($book->file_path);
    }

    public function test_admin_rejects_a_file_that_is_not_a_real_pdf(): void
    {
        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first();

        $this->actingAs($admin)->from(route('admin.books.create'))->post(route('admin.books.store'), [
            'title_en' => 'Fake Ebook',
            'description_en' => 'Description.',
            'author' => 'Author Name',
            'status' => Book::STATUS_PUBLISHED,
            'price' => '15000',
            'currency' => 'TZS',
            'pdf' => UploadedFile::fake()->createWithContent('fake.pdf', 'not a real pdf file'),
        ])->assertSessionHasErrors('pdf');

        $this->assertDatabaseMissing('books', ['title_en' => 'Fake Ebook']);
    }

    public function test_book_with_purchases_cannot_be_deleted(): void
    {
        $user = $this->makeMember();
        $book = $this->makeBook();

        $response = $this->actingAs($user)->post(route('books.buy', $book), [
            'payment_method' => 'mobile_money',
        ]);
        preg_match('#/payment/sandbox/([^/]+)#', $response->headers->get('Location'), $matches);
        $providerReference = $matches[1];
        $ledger = SandboxPayment::where('provider_reference', $providerReference)->first();
        $payment = Payment::where('transaction_reference', $ledger->transaction_reference)->first();

        $this->actingAs($user)->post("/payment/sandbox/{$providerReference}/confirm");
        $this->actingAs($user)->get(route('payment.callback', [
            'transaction_reference' => $payment->transaction_reference,
            'provider_reference' => $providerReference,
        ]));

        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first();

        $this->actingAs($admin)->delete(route('admin.books.destroy', $book))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }

    public function test_download_of_a_missing_pdf_returns_404_not_a_server_error(): void
    {
        $user = $this->makeMember();
        $book = $this->makeBook();

        $this->markBookPaid($user, $book);

        $book->update(['file_path' => 'books/does-not-exist.pdf']);

        Storage::disk('local')->assertMissing('books/does-not-exist.pdf');

        $this->actingAs($user)->get(route('books.download', $book))->assertNotFound();
        $this->actingAs($user)->get(route('books.read', $book))->assertNotFound();
    }

    public function test_library_hides_download_button_when_the_book_has_no_pdf(): void
    {
        $user = $this->makeMember();
        $book = $this->makeBookWithoutFile();

        $this->markBookPaid($user, $book);

        $this->actingAs($user)->get(route('member.library'))
            ->assertOk()
            ->assertSee($book->title_en)
            ->assertDontSee(route('books.download', $book));
    }

    public function test_owner_can_download_only_after_a_verified_paid_payment(): void
    {
        $user = $this->makeMember();
        $book = $this->makeBook();

        $this->actingAs($user)->get(route('books.download', $book))->assertForbidden();

        $this->markBookPaid($user, $book);

        $response = $this->actingAs($user)->get(route('books.download', $book));

        $response->assertOk();
        $this->assertSame(self::SAMPLE_PDF, $response->streamedContent());
    }
}
