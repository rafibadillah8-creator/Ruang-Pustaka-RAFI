<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed']); // persentase atau nominal tetap
            $table->decimal('value', 10, 2); // 10 (untuk 10%) atau 10000 (untuk Rp10.000)
            $table->enum('scope', ['all', 'specific'])->default('all'); // berlaku semua buku / buku tertentu
            $table->integer('usage_limit')->nullable(); // null = tidak terbatas
            $table->integer('used_count')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('book_voucher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_voucher');
        Schema::dropIfExists('vouchers');
    }
};