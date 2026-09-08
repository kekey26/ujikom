<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPinjam extends Model
{
    use HasFactory;

    protected $table = 'detail_pinjam'; // Sesuaikan nama tabel

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}