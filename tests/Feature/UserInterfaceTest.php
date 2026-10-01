<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;

class UserInterfaceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Menguji Tampilan Halaman Utama (Homepage)
     */
    public function test_homepage_ui_displays_correct_sections_and_inputs()
    {
        // 1. Buat data dummy agar tampilan "Buku Populer" dan "Buku Baru" ter-render
        $category = Category::create(['name' => 'Umum']);
        Book::create([
            'title' => 'Buku Dummy Homepage',
            'author' => 'Penulis Dummy',
            'price' => 20000,
            'category_id' => $category->id,
            'file_path' => 'books/dummy.pdf'
        ]);

        $response = $this->get('/');
        
        $response->assertStatus(200); 
        
        // Mengecek apakah bagian penting muncul di layar
        $response->assertSee('Buku Populer');
        $response->assertSee('Buku Baru');
        
        // Mengecek keberadaan kotak input pencarian (menggunakan potongan teks agar akurat)
        $response->assertSee('Cari judul, penulis');
    }

    /**
     * Menguji Tampilan Form Login
     */
    public function test_login_page_ui_has_correct_input_forms()
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        
        $response->assertSee('name="email"', false); 
        $response->assertSee('name="password"', false);
        $response->assertSee('type="submit"', false);
    }

    /**
     * Menguji Tampilan Detail Buku & Input Voucher (Hanya untuk User Login)
     */
    public function test_book_detail_page_ui_shows_book_info_and_voucher_input_for_logged_in_user()
    {
        $category = Category::create(['name' => 'Sastra']);
        $book = Book::create([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'price' => 75000,
            'category_id' => $category->id,
            'file_path' => 'books/dummy.pdf',
            'cover_image' => 'default_cover.jpg',
        ]);

        // Buat user dummy dan lakukan Login
        $user = User::factory()->create();

        // Kunjungi halaman detail buku sebagai user yang sudah login
        $response = $this->actingAs($user)->get('/books/' . $book->id);
        
        $response->assertStatus(200);
        
        $response->assertSee('Laskar Pelangi');
        $response->assertSee('Andrea Hirata');
        $response->assertSee('75.000'); 
        
        // Karena sudah login, form voucher harusnya muncul!
        $response->assertSee('Punya Kode Voucher?');
    }
}
