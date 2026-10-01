<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Categories::withCount('books')->orderBy('name')->get();

        return view('kategori.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:categories,name']]);
        $data['slug'] = Str::slug($data['name']);
        Categories::create($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categories $category): View
    {
        return view('kategori.show', ['category' => $category->load('books')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categories $category): View
    {
        return view('kategori.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categories $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category)],
        ]);
        $data['slug'] = Str::slug($data['name']);
        $category->update($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categories $category): RedirectResponse
    {
        if ($category->books()->withTrashed()->exists()) {
            return redirect()->route('kategori.index')->with('error', 'Kategori yang masih memiliki buku tidak dapat dihapus.');
        }

        $category->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
