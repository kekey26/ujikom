<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    public function indexPeminjaman(Request $request)
{
    $search = $request->input('search');

    $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
        ->where('status', 'diajukan')
        ->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
}
    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.katalog', compact('alats'));
    }

    public function dashboard()
    {
        $userId = auth()->id();

        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();
        $peminjamanDiajukan = Peminjaman::where('user_id', $userId)
            ->where('status', 'diajukan')
            ->count();
        $sedangDipinjam = Peminjaman::where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->count();
        $alatTersedia = Alat::where('stok', '>', 0)->count();

        return view('peminjam.dashboard', compact(
            'totalPeminjaman',
            'peminjamanDiajukan',
            'sedangDipinjam',
            'alatTersedia'
        ));
    }

    public function detailAlat(Alat $alat)
    {
        $alat->load('kategori');

        return view('peminjam.detail-alat', compact('alat'));
    }

    public function formPeminjaman()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();

        return view('peminjam.peminjaman', compact('alats'));
    }

    public function halamanPengembalian()
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat.kategori')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->get();

        return view('peminjam.pengembalian', compact('peminjaman'));
    }

    public function ajukanPengembalian($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())->findOrFail($id);

        if (!in_array($peminjaman->status, ['dipinjam', 'telat'], true)) {
            return redirect()->route('peminjam.pengembalian')
                ->with('error', 'Peminjaman ini tidak dapat diajukan untuk pengembalian.');
        }

        if ($peminjaman->pengembalian_diajukan_at) {
            return redirect()->route('peminjam.pengembalian')
                ->with('success', 'Pengajuan pengembalian untuk peminjaman ini sudah dikirim.');
        }

        $updated = Peminjaman::whereKey($peminjaman->id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['dipinjam', 'telat'])
            ->whereNull('pengembalian_diajukan_at')
            ->update(['pengembalian_diajukan_at' => now()]);

        if (!$updated) {
            return redirect()->route('peminjam.pengembalian')
                ->with('error', 'Pengajuan tidak dapat diproses. Muat ulang halaman dan coba lagi.');
        }

        return redirect()->route('peminjam.pengembalian')
            ->with('success', 'Pengajuan pengembalian berhasil dikirim. Silakan serahkan alat kepada Petugas.');
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // Buat header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // Masukkan daftar alat yang dipinjam ke detail_pinjam
            foreach ($request->alat_id as $index => $alatId) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $request->jumlah[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('peminjam.riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjaman = Peminjaman::with([
            'detailPinjam.alat.kategori',
            'pengembalian',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjaman'));
    }
}