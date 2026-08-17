<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\BookPurchaseService;
use App\Services\Payments\Contracts\PaymentServiceInterface;
use App\Services\Payments\Exceptions\PaymentException;
use App\Services\Payments\PaymentTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $featured = Book::where('is_featured', true)->first()
            ?? Book::published()->latest('id')->first();

        $others = Book::published()
            ->where('id', '!=', $featured?->id ?? 0)
            ->orderBy('publication_date', 'desc')
            ->get();

        $comingSoon = Book::where('status', 'coming_soon')
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.books', compact('featured', 'others', 'comingSoon'));
    }

    public function show(Book $book)
    {
        $purchases = app(BookPurchaseService::class);

        $owned = auth()->check() && auth()->user()->isMember()
            && $purchases->alreadyPurchased(auth()->user(), $book);

        $pendingPayment = auth()->check() && auth()->user()->isMember()
            ? $purchases->pendingPayment(auth()->user(), $book)
            : null;

        return view('pages.books-show', compact('book', 'owned', 'pendingPayment'));
    }

    public function purchase(Book $book)
    {
        abort_unless($book->isPurchasable(), 404);

        return view('pages.book-purchase', compact('book'));
    }

    public function guestCheckout(Request $request, Book $book)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'payment_method' => ['required', 'string'],
        ]);

        try {
            $payment = app(PaymentTransactionService::class)->createGuestBookPayment([
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'],
                'phone' => $validated['customer_phone'],
            ], $book, $validated['payment_method']);
        } catch (PaymentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $initiation = app(PaymentServiceInterface::class)->initiate($payment, $validated['payment_method']);

        return redirect()->away($initiation->checkoutUrl);
    }

    public function guestDownload(string $token)
    {
        $purchase = app(BookPurchaseService::class)->purchaseByDownloadToken($token);

        abort_unless($purchase, 403);

        $book = $purchase->book;

        abort_unless($book->hasFile(), 404);

        abort_unless(Storage::disk('local')->exists($book->file_path), 404);

        Log::info('Guest book downloaded', [
            'purchase_id' => $purchase->id,
            'book_id' => $book->id,
            'book' => $book->title_en,
            'customer_email' => $purchase->customer_email,
        ]);

        return Storage::disk('local')->download($book->file_path, $book->title_en.'.pdf');
    }
}
