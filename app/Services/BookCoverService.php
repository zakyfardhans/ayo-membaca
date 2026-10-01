<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookCoverService
{
    public function upload(UploadedFile $file): string
    {
        return $file->store('covers', 'public');
    }

    public function uploadPdf(UploadedFile $file): string
    {
        return $file->store('books', 'public');
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function deletePdf(?string $path): void
    {
        $this->delete($path);
    }
}
