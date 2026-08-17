<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\User;
use App\Services\BookPurchaseService;
use App\Services\Payments\Contracts\PaymentServiceInterface;
use App\Services\Payments\Exceptions\PaymentException;
use App\Services\Payments\PaymentTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function checkout(Request $request, Book $book)
    {
        abort_unless($book->isPurchasable(), 404);

        $purchases = app(BookPurchaseService::class);

        abort_if($purchases->alreadyPurchased($request->user(), $book), 409);

        return view('member.book-checkout', compact('book'));
    }

    public function buy(Request $request, Book $book)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string'],
        ]);

        $user = $request->user();

        try {
            $payment = app(PaymentTransactionService::class)->createBookPayment($user, $book, $validated['payment_method']);
        } catch (PaymentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $initiation = app(PaymentServiceInterface::class)->initiate($payment, $validated['payment_method']);

        return redirect()->away($initiation->checkoutUrl);
    }

    public function library(Request $request)
    {
        $purchases = BookPurchase::query()
            ->with(['book' => fn ($q) => $q->withCount('purchases')])
            ->where('user_id', $request->user()->id)
            ->latest('purchased_at')
            ->get();

        return view('member.library', compact('purchases'));
    }

    public function read(Request $request, Book $book)
    {
        $this->authorizeBookAccess($request->user(), $book);

        return Storage::disk('local')->response($book->file_path, $book->title_en.'.pdf', [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }

    public function download(Request $request, Book $book)
    {
        $this->authorizeBookAccess($request->user(), $book);

        Log::info('Book downloaded', [
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'book' => $book->title_en,
        ]);

        return Storage::disk('local')->download($book->file_path, $book->title_en.'.pdf');
    }

    protected function authorizeBookAccess(User $user, Book $book): void
    {
        abort_unless($book->hasFile(), 404);

        abort_unless(Storage::disk('local')->exists($book->file_path), 404);

        abort_unless(app(BookPurchaseService::class)->hasAccess($user, $book), 403);
    }
}
