@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header-title', 'Manajemen Pengembalian')

@section('content')
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-indigo-100/80 bg-white shadow-sm">
        <div class="border-b border-indigo-50 bg-indigo-50/20 p-5">
            <h3 class="font-bold text-slate-800">Daftar Pengembalian Alat</h3>
            <p class="mt-0.5 text-xs text-slate-500">Kelola pengembalian alat yang masih dipinjam secara manual.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-indigo-50 bg-indigo-50/40 text-xs uppercase tracking-wider text-slate-400">
                        <th class="px-4 py-3.5 font-semibold">Peminjam</th>
                        <th class="px-4 py-3.5 font-semibold">Alat</th>
                        <th class="px-4 py-3.5 font-semibold">Tanggal Pinjam</th>
                        <th class="px-4 py-3.5 font-semibold">Rencana Kembali</th>
                        <th class="px-4 py-3.5 font-semibold">Keterlambatan</th>
                        <th class="px-4 py-3.5 font-semibold">Denda</th>
                        <th class="px-4 py-3.5 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    @forelse($peminjaman as $pinjam)
                        @php
                            $tanggalKembali = \Carbon\Carbon::parse($pinjam->tgl_kembali_plan);
                            $hariIni = \Carbon\Carbon::today();
                            $terlambat = $hariIni->gt($tanggalKembali);
                            $hariTerlambat = $terlambat ? $tanggalKembali->diffInDays($hariIni) : 0;
                            $dendaPerHari = 5000;
                            $perkiraanDenda = $hariTerlambat * $dendaPerHari;
                        @endphp

                        <tr class="align-top transition hover:bg-indigo-50/30">
                            <td class="px-4 py-4 font-medium text-slate-800">
                                <div class="font-semibold text-slate-900">{{ $pinjam->user->name ?? 'User Dihapus' }}</div>
                                @if($pinjam->user)
                                    <div class="mt-0.5 text-xs text-slate-400">{{ $pinjam->user->email }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="space-y-1.5">
                                    @foreach($pinjam->detailPinjam as $detail)
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-slate-700">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-600">{{ $detail->jumlah }} pcs</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs text-slate-500"><span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d-m-Y') }}</span></td>
                            <td class="px-4 py-4">
                                <div class="text-xs font-medium text-slate-800">{{ $tanggalKembali->format('d-m-Y') }}</div>
                                <span class="mt-1 inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $terlambat ? 'border-red-100 bg-red-50 text-red-600' : 'border-emerald-100 bg-emerald-50 text-emerald-600' }}">
                                    {{ $terlambat ? 'Terlambat' : 'Belum terlambat' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                @if($hariTerlambat > 0)
                                    <span class="text-xs font-semibold text-red-600">{{ $hariTerlambat }} hari</span>
                                @else
                                    <span class="text-xs text-slate-400">0 hari</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($perkiraanDenda > 0)
                                    <span class="text-xs font-bold text-red-600">Rp {{ number_format($perkiraanDenda, 0, ',', '.') }}</span>
                                    <div class="mt-0.5 text-[11px] text-slate-400">Rp {{ number_format($dendaPerHari, 0, ',', '.') }}/hari</div>
                                @else
                                    <span class="text-xs font-semibold text-emerald-600">Rp 0</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('admin.pengembalian.create', $pinjam->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm shadow-indigo-600/25 transition hover:bg-indigo-700">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Kembalikan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="text-sm font-semibold text-slate-600">Tidak ada peminjaman yang perlu dikembalikan.</div>
                                <div class="mt-1 text-xs text-slate-400">Semua alat sudah dikembalikan.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">Menampilkan {{ $peminjaman->firstItem() ?? 0 }}-{{ $peminjaman->lastItem() ?? 0 }} dari {{ $peminjaman->total() }} peminjaman</p>
                {{ $peminjaman->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endsection
