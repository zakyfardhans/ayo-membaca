<?php

use App\Http\Controllers\AiBookController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CategoriesController;
use App\Models\Books;
use App\Models\Categories;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard', [
        'bookCount' => Books::count(),
        'categoryCount' => Categories::count(),
        'availableCount' => Books::where('stock', '>', 0)->count(),
        'lowStockCount' => Books::whereBetween('stock', [1, 2])->count(),
        'emptyStockCount' => Books::where('stock', 0)->count(),
        'trashCount' => Books::onlyTrashed()->count(),
        'recentBooks' => Books::with('category')->latest()->take(5)->get(),
        'categories' => Categories::withCount('books')->orderBy('name')->get(),
    ]);
})->name('dashboard');

Route::resource('kategori', CategoriesController::class)->parameters(['kategori' => 'category']);
Route::resource('buku', BooksController::class)->parameters(['buku' => 'book']);
Route::get('/buku/{book}/read', [BooksController::class, 'read'])->name('buku.read');
Route::post('/buku/{book}/ai/chat', [AiBookController::class, 'chat'])->middleware('throttle:12,1')->name('buku.ai.chat');
Route::post('/buku/{book}/ai/metadata', [AiBookController::class, 'metadata'])->middleware('throttle:6,1')->name('buku.ai.metadata');
Route::post('/ai/catalog', [AiBookController::class, 'catalog'])->middleware('throttle:8,1')->name('ai.catalog');
Route::get('/trash', [BooksController::class, 'trash'])->name('buku.trash');
Route::patch('/trash/{book}/restore', [BooksController::class, 'restore'])->withTrashed()->name('buku.restore');
Route::delete('/trash/{book}/force-delete', [BooksController::class, 'forceDelete'])->withTrashed()->name('buku.forceDelete');
