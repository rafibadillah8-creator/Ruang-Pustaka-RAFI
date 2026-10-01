<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryAndBookSeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Pelajaran Sekolah']);
        Category::create(['name' => 'Novel & Fiksi']);
        Category::create(['name' => 'Teknologi & Komputer']);
        Category::create(['name' => 'Komik']);
        
        // Tambahkan kategori baru di sini:
        Category::create(['name' => 'Sejarah & Budaya']); 
    }
}