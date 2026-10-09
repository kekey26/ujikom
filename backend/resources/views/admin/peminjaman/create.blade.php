@extends('layouts.app')

@section('title', 'Tambah Peminjaman - Panel Admin')
@section('header-title', 'Form Tambah Transaksi Peminjaman')

@section('content')
    @if(session('error'))
        <div class="mx-auto mb-4 max-w-2xl rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mx-auto mb-4 max-w-2xl rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="mx-auto max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form action="{{ route('admin.peminjaman.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="user_id" class="mb-2 block text-sm font-semibold text-slate-700">Pilih Peminjam (User)</label>
                <select id="user_id" name="user_id" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">-- Pilih User --</option>
                    @foreach($user as $item)
                        <option value="{{ $item->id }}" {{ old('user_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="tgl_pinjam" class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Pinjam</label>
                    <input id="tgl_pinjam" type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', now()->format('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label for="tgl_kembali_plan" class="mb-2 block text-sm font-semibold text-slate-700">Rencana Tanggal Kembali</label>
                    <input id="tgl_kembali_plan" type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan') }}" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <div class="mb-2 flex items-center justify-between">
                <label class="block text-sm font-semibold text-slate-700">Daftar Alat yang Dipinjam</label>
            </div>

            <div id="alat-list" class="space-y-3">
                <div class="alat-row flex items-center gap-2">
                    <select name="alat_id[]" required class="alat-select min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($alat as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_alat }} (stok: {{ $item->stok }})</option>
                        @endforeach
                    </select>
                    <input type="number" name="jumlah[]" value="1" min="1" required aria-label="Jumlah alat"
                        class="w-24 rounded-lg border border-slate-200 px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <button type="button" class="remove-alat h-9 w-9 shrink-0 rounded-lg bg-red-500 text-lg font-bold text-white transition hover:bg-red-600" title="Hapus alat">&times;</button>
                </div>
            </div>

            <button type="button" id="add-alat" class="mt-3 rounded-lg bg-slate-800 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">
                + Tambah Alat Lain
            </button>

            @if($alat->isEmpty())
                <p class="mt-3 text-sm text-red-600">Tidak ada alat dengan stok tersedia.</p>
            @endif

            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('admin.peminjaman.index') }}" class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-300">Batal</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Simpan Peminjaman</button>
            </div>
        </form>
    </div>

    <template id="alat-option-template">
        <option value="">-- Pilih Alat --</option>
        @foreach($alat as $item)
            <option value="{{ $item->id }}">{{ $item->nama_alat }} (stok: {{ $item->stok }})</option>
        @endforeach
    </template>

    <script>
        const alatList = document.getElementById('alat-list');
        const alatOptions = document.getElementById('alat-option-template').innerHTML;

        document.getElementById('add-alat').addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'alat-row flex items-center gap-2';
            row.innerHTML = `
                <select name="alat_id[]" required class="alat-select min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">${alatOptions}</select>
                <input type="number" name="jumlah[]" value="1" min="1" required aria-label="Jumlah alat" class="w-24 rounded-lg border border-slate-200 px-3 py-3 text-sm text-slate-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <button type="button" class="remove-alat h-9 w-9 shrink-0 rounded-lg bg-red-500 text-lg font-bold text-white transition hover:bg-red-600" title="Hapus alat">&times;</button>
            `;
            alatList.appendChild(row);
        });

        alatList.addEventListener('click', function (event) {
            if (event.target.closest('.remove-alat') && alatList.children.length > 1) {
                event.target.closest('.alat-row').remove();
            }
        });
    </script>
@endsection
