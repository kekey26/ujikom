@extends('layouts.app')

@section('title', 'Cetak Laporan - Dashboard Petugas')
@section('header-title', 'Laporan Peminjaman Alat')

@section('content')
    <style>
        @media print {
            @page {
                margin: 12mm;
            }

            html,
            body,
            .h-screen,
            .overflow-hidden,
            main {
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            body {
                display: block !important;
                margin: 0 !important;
                background: #fff !important;
            }

            body > .h-screen {
                display: block !important;
            }

            .h-screen > aside,
            .h-screen > div > header {
                display: none !important;
            }

            .h-screen > div {
                display: block !important;
                width: 100% !important;
                overflow: visible !important;
            }

            body * {
                visibility: hidden !important;
            }

            .no-print {
                display: none !important;
            }

            main {
                display: block !important;
                position: static !important;
                padding: 0 !important;
            }

            .print-report,
            .print-report * {
                visibility: visible !important;
            }

            .print-report {
                position: static !important;
                width: 100% !important;
                margin: 0 !important;
            }

            .print-report-card {
                margin-bottom: 12px !important;
                border: 1px solid #d1d5db !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .print-report table {
                width: 100% !important;
            }

            .print-report .overflow-x-auto {
                overflow: visible !important;
            }

            .print-report tr {
                break-inside: avoid;
            }
        }
    </style>

    <div class="print-report">
        <div class="print-report-card bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Rekap Data Peminjaman</h3>
                    <p class="text-slate-500 text-sm">
                        Cetak data riwayat peminjaman laboratorium
                        @if($statusFilter)
                            dengan status {{ $statusFilter === 'dipinjam' ? 'Sedang Dipinjam' : ucfirst($statusFilter) }}.
                        @else
                            sesuai status.
                        @endif
                    </p>
                </div>
                <div class="no-print flex flex-col sm:flex-row sm:items-center gap-2">
                    <form action="{{ route('petugas.laporan.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                        <label for="status" class="text-sm font-medium text-slate-600">Status</label>
                        <select id="status" name="status" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="" @selected($statusFilter === '')>Semua Status</option>
                            <option value="diajukan" @selected($statusFilter === 'diajukan')>Diajukan</option>
                            <option value="dipinjam" @selected($statusFilter === 'dipinjam')>Sedang Dipinjam</option>
                            <option value="telat" @selected($statusFilter === 'telat')>Terlambat</option>
                            <option value="dikembalikan" @selected($statusFilter === 'dikembalikan')>Dikembalikan</option>
                            <option value="selesai" @selected($statusFilter === 'selesai')>Selesai</option>
                        </select>
                        <button type="submit" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                            Terapkan
                        </button>
                    </form>
                    <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        Cetak Laporan
                    </button>
                </div>
            </div>
        </div>

        <div class="print-report-card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3.5 px-4 font-semibold">No</th>
                            <th class="py-3.5 px-4 font-semibold">Peminjam</th>
                            <th class="py-3.5 px-4 font-semibold">Tgl Pinjam</th>
                            <th class="py-3.5 px-4 font-semibold">Rencana Kembali</th>
                            <th class="py-3.5 px-4 font-semibold">Tanggal Dikembalikan</th>
                            <th class="py-3.5 px-4 font-semibold">Alat</th>
                            <th class="py-3.5 px-4 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @forelse($peminjaman as $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-medium text-slate-500">{{ $peminjaman->firstItem() + $loop->index }}</td>
                                <td class="py-3.5 px-4 font-medium text-slate-900">{{ $item->user->name ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $item->tgl_pinjam }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $item->tgl_kembali_plan }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $item->pengembalian?->tgl_kembali ?? '-' }}</td>
                                <td class="py-3.5 px-4">
                                    <ul class="list-disc list-inside text-xs text-slate-600">
                                        @foreach($item->detailPinjam as $detail)
                                            <li>{{ $detail->alat->nama_alat ?? 'Alat' }} ({{ $detail->jumlah }} pcs)</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada data untuk laporan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection