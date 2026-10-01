<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Books;
use App\Models\Categories;
use App\Services\BookCoverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $filters = request()->only(['search', 'category', 'stock']);
        $books = Books::with('category')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category_id', $category))
            ->when(($filters['stock'] ?? null) === 'available', fn ($query) => $query->where('stock', '>', 0))
            ->when(($filters['stock'] ?? null) === 'empty', fn ($query) => $query->where('stock', 0))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('buku.index', [
            'books' => $books,
            'categories' => Categories::orderBy('name')->get(),
            'filters' => $filters,
            'bookCount' => Books::count(),
            'lowStockCount' => Books::whereBetween('stock', [1, 2])->count(),
            'emptyStockCount' => Books::where('stock', 0)->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('buku.create', ['categories' => Categories::orderBy('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request, BookCoverService $coverService): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $coverService->upload($request->file('cover_image'));
        }
        if ($request->hasFile('pdf_file')) {
            $data['pdf_file'] = $coverService->uploadPdf($request->file('pdf_file'));
        }

        Books::create($data);

        return redirect()->route('buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Books $book): View
    {
        return view('buku.show', ['book' => $book->load('category')]);
    }

    public function read(Books $book)
    {
        abort_unless($book->pdf_file && Storage::disk('public')->exists($book->pdf_file), 404);

        return response()->file(Storage::disk('public')->path($book->pdf_file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($book->pdf_file).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Books $book): View
    {
        return view('buku.edit', [
            'book' => $book,
            'categories' => Categories::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Books $book, BookCoverService $coverService): RedirectResponse
    {
        $data = $request->validated();
        $oldCover = $book->cover_image;
        $oldPdf = $book->pdf_file;
        $newCover = null;
        $newPdf = null;

        if ($request->hasFile('cover_image')) {
            $newCover = $coverService->upload($request->file('cover_image'));
            $data['cover_image'] = $newCover;
        }
        if ($request->hasFile('pdf_file')) {
            $newPdf = $coverService->uploadPdf($request->file('pdf_file'));
            $data['pdf_file'] = $newPdf;
        }

        try {
            $book->update($data);
        } catch (\Throwable $exception) {
            if ($newCover) {
                Storage::disk('public')->delete($newCover);
            }
            if ($newPdf) {
                Storage::disk('public')->delete($newPdf);
            }

            throw $exception;
        }

        if ($newCover && $oldCover) {
            $coverService->delete($oldCover);
        }
        if ($newPdf && $oldPdf) {
            $coverService->deletePdf($oldPdf);
        }

        return redirect()->route('buku.show', $book)->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Books $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dipindahkan ke Trash.');
    }

    public function trash(): View
    {
        return view('buku.trash', [
            'books' => Books::onlyTrashed()->with('category')->latest('deleted_at')->paginate(10),
        ]);
    }

    public function restore(Books $book): RedirectResponse
    {
        $book->restore();

        return redirect()->route('buku.trash')->with('success', 'Buku berhasil dipulihkan.');
    }

    public function forceDelete(Books $book, BookCoverService $coverService): RedirectResponse
    {
        $coverService->delete($book->cover_image);
        $coverService->deletePdf($book->pdf_file);
        $book->forceDelete();

        return redirect()->route('buku.trash')->with('success', 'Buku berhasil dihapus secara permanen.');
    }
}
