<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;

class WebsiteFlowTest extends TestCase
{
    // Menggunakan RefreshDatabase agar database pengujian selalu bersih dan 
    // tidak mengganggu database asli Anda.
    use RefreshDatabase;

    /**
     * Uji Coba Role Guest: Registrasi
     */
    public function test_guest_can_register_and_data_enters_users_table()
    {
        // 1. Simulasi Guest mengirim data registrasi
        $response = $this->post('/register', [
            'name' => 'Pembaca Baru',
            'email' => 'pembacabaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 2. Pastikan diarahkan ke halaman utama setelah sukses
        $response->assertRedirect('/');

        // 3. Pastikan data benar-benar masuk ke database (Tabel users)
        $this->assertDatabaseHas('users', [
            'name' => 'Pembaca Baru',
            'email' => 'pembacabaru@example.com',
        ]);
    }

    /**
     * Uji Coba Role User: Tambah Wishlist
     */
    public function test_user_can_add_book_to_wishlist_and_data_enters_wishlists_table()
    {
        // 1. Buat dummy user dan dummy book
        $user = User::factory()->create();
        
        // Buat kategori dummy terlebih dahulu karena wajib
        $category = Category::create(['name' => 'Edukasi']);

        // Buat buku dengan menyertakan seluruh field yang wajib (NOT NULL)
        $book = Book::create([
            'title' => 'Buku Belajar Laravel',
            'author' => 'Developer',
            'price' => 50000,
            'category_id' => $category->id,
            'file_path' => 'books/dummy.pdf', // Field ini wajib tidak boleh kosong
            'cover_image' => 'default_cover.jpg',
        ]);

        // 2. Simulasi User login dan mengakses route wishlist
        $response = $this->actingAs($user)->post('/wishlist/' . $book->id);

        // 3. Pastikan data masuk ke tabel wishlists
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /**
     * Uji Coba Role Admin: Tambah Buku
     */
    public function test_admin_can_create_book_and_data_enters_books_table()
    {
        // 1. Buat akun Admin
        $admin = User::factory()->create([
            'email' => 'rafibadillah8@gmail.com', // Sesuai dengan konfigurasi hardcode Anda
        ]);

        // Buat kategori dummy
        $category = Category::create(['name' => 'Teknologi']);

        // 2. Simulasi Admin mengirim data buku baru
        $response = $this->actingAs($admin)->post('/admin/books', [
            'title' => 'Buku Mastering PHP',
            'author' => 'Rafi Badillah',
            'price' => 100000,
            'category_ids' => [$category->id]
        ]);

        // 3. Pastikan data berhasil masuk ke database (Tabel books)
        $this->assertDatabaseHas('books', [
            'title' => 'Buku Mastering PHP',
            'author' => 'Rafi Badillah',
            'price' => 100000,
        ]);

        // Pastikan relasi kategori buku (pivot table) juga terisi
        $book = Book::where('title', 'Buku Mastering PHP')->first();
        $this->assertDatabaseHas('book_category', [
            'book_id' => $book->id,
            'category_id' => $category->id,
        ]);
    }
}
