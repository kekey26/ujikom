<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'selesai', 'dikembalikan', 'telat') NOT NULL DEFAULT 'diajukan'");
    }

    public function down(): void
    {
        DB::statement("UPDATE peminjaman SET status = 'dikembalikan' WHERE status = 'selesai'");
        DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'dikembalikan', 'telat') NOT NULL DEFAULT 'diajukan'");
    }
};