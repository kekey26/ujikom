<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPinjam extends Model
{
    use HasFactory;

    protected $table = 'detail_pinjam'; // Sesuaikan nama tabel

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
    ];

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}