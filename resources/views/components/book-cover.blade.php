@props(['book', 'size' => 'small'])
@php
    $coverExists = $book->cover_image && Illuminate\Support\Facades\Storage::disk('public')->exists($book->cover_image);
@endphp
<div @class([
    'cover-thumb',
    'cover-large' => $size === 'large',
    'palette-' . $book->id % 6,
])>
    @if ($coverExists)
        <img src="/storage/{{ ltrim($book->cover_image, '/') }}" alt="Sampul {{ $book->title }}">
    @else
        <span>{{ Illuminate\Support\Str::limit($book->title, 46) }}</span>
    @endif
</div>
