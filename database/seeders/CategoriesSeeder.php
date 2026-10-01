<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Fiksi', 'Pemrograman', 'Sejarah', 'Sains', 'Filsafat', 'Teknologi'] as $name) {
            Categories::updateOrCreate(
                ['slug' => str()->slug($name)],
                ['name' => $name]
            );
        }
    }
}
