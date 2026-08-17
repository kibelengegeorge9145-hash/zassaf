<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Payment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::withCount('purchases')->orderByDesc('id')->get();

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        return view('admin.books.form', ['book' => new Book()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('books', 'public');
        }

        if ($request->hasFile('pdf')) {
            $data['file_path'] = $this->storePdf($request->file('pdf'));
        }

        Book::create($data);

        return redirect()->route('admin.books.index')
            ->with('success', __('admin.saved'));
    }

    public function edit(Book $book)
    {
        return view('admin.books.form', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $this->validated($request);

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }

            $data['cover_path'] = $request->file('cover')->store('books', 'public');
        }

        if ($request->hasFile('pdf')) {
            $data['file_path'] = $this->storePdf($request->file('pdf'));

            if ($book->file_path) {
                Storage::disk('local')->delete($book->file_path);
            }
        }

        $book->update($data);

        return redirect()->route('admin.books.index')
            ->with('success', __('admin.saved'));
    }

    public function destroy(Book $book)
    {
        if ($book->purchases()->exists()) {
            return back()->with('error', __('admin.books.has_purchases'));
        }

        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        if ($book->file_path) {
            Storage::disk('local')->delete($book->file_path);
        }

        $book->delete();

        return back()->with('success', __('admin.deleted'));
    }

    public function purchases()
    {
        $purchases = BookPurchase::query()
            ->with(['user', 'book', 'payment'])
            ->latest('purchased_at')
            ->paginate(20);

        $totals = [
            'count' => BookPurchase::count(),
            'revenue' => Payment::where('payment_type', Payment::TYPE_BOOK)
                ->where('status', Payment::STATUS_PAID)
                ->sum('amount'),
            'books' => Book::withCount('purchases')->get()->sum('purchases_count'),
        ];

        return view('admin.books.purchases', compact('purchases', 'totals'));
    }

    private function storePdf(UploadedFile $pdf): string
    {
        return $pdf->store('books', 'local');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title_en' => ['required', 'string', 'max:255'],
            'title_sw' => ['nullable', 'string', 'max:255'],
            'description_en' => ['required', 'string', 'max:4000'],
            'description_sw' => ['nullable', 'string', 'max:4000'],
            'author' => ['required', 'string', 'max:255'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:2048', $this->assertRealPdf()],
            'status' => ['required', 'string', Rule::in(['featured', 'published', 'preorder', 'coming_soon'])],
            'publication_date' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
        ]);

        $data['currency'] = $data['currency'] ?: 'TZS';
        $data['preorder_enabled'] = $request->boolean('preorder_enabled');
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }

    private function assertRealPdf(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! $value instanceof UploadedFile) {
                return;
            }

            $handle = @fopen($value->getRealPath(), 'rb');

            if ($handle === false) {
                $fail(__('admin.books.fields.pdf_invalid'));

                return;
            }

            $signature = (string) fread($handle, 5);
            fclose($handle);

            if (! str_starts_with($signature, '%PDF-')) {
                $fail(__('admin.books.fields.pdf_invalid'));
            }
        };
    }
}
