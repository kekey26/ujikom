<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman'; // Sesuaikan nama tabel jika berbeda

    protected $fillable = [
        'user_id',
        'tgl_pinjam',
        'tgl_kembali_plan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date:Y-m-d',
            'tgl_kembali_plan' => 'date:Y-m-d',
        ];
    }

    // Relasi ke Model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Model DetailPinjam (HasMany)
    public function detailPinjam()
    {
        return $this->hasMany(DetailPinjam::class, 'peminjaman_id');
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }
}