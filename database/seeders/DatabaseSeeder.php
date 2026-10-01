<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun pertama sebagai Admin otomatis
        User::create([
            'name' => 'Rafi Badillah',
            'email' => 'rafibadillah8@gmail.com',
            'password' => Hash::make('password'), // Ganti password sesuai keinginan
            'role' => 'admin'
        ]);

        // Kategori Buku
        Category::create(['name' => 'Pelajaran Sekolah']);
        Category::create(['name' => 'Novel & Fiksi']);
        Category::create(['name' => 'Teknologi & Komputer']);
        Category::create(['name' => 'Komik']);
        Category::create(['name' => 'Sejarah & Budaya']);
    }
}