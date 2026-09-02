<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Book;

class CategoryAndBookSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kategori Contoh
        $pelajaran = Category::create(['name' => 'Pelajaran Sekolah']);
        $novel     = Category::create(['name' => 'Novel & Fiksi']);
        $teknologi = Category::create(['name' => 'Teknologi & Komputer']);

        // 2. Buat Dummy Buku
        Book::create([
            'category_id' => $pelajaran->id,
            'title'       => 'Pemrograman Web dengan Laravel',
            'author'      => 'Tim Guru SMK',
            'publisher'   => 'Informatika Press',
            'description' => 'Buku panduan dasar belajar Laravel untuk siswa SMK jurusan RPL.',
            'price'       => 0, // Gratis
            'cover_image' => 'default_cover.jpg',
            'file_path'   => 'ebooks/sample.pdf',
        ]);

        Book::create([
            'category_id' => $novel->id,
            'title'       => 'Laskar Pelangi',
            'author'      => 'Andrea Hirata',
            'publisher'   => 'Bentang Pustaka',
            'description' => 'Kisah perjuangan 10 anak di Belitung dalam menuntut ilmu.',
            'price'       => 25000,
            'cover_image' => 'default_cover.jpg',
            'file_path'   => 'ebooks/sample.pdf',
        ]);

        Book::create([
            'category_id' => $teknologi->id,
            'title'       => 'Dasar-Dasar Jaringan Komputer',
            'author'      => 'Ahmad Hanafi',
            'publisher'   => 'Tekno Media',
            'description' => 'Panduan lengkap konfigurasi IP, Subnetting, dan Routing.',
            'price'       => 15000,
            'cover_image' => 'default_cover.jpg',
            'file_path'   => 'ebooks/sample.pdf',
        ]);
    }
}