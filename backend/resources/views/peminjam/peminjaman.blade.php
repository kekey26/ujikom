@extends('layouts.app')

@section('title', 'Mengajukan Peminjaman - Panel Peminjam')
@section('header-title', 'Mengajukan Peminjaman')

@section('content')
    @php
        $selectedAlatIds = old('alat_id', []);
        $selectedQuantities = old('jumlah', []);
        $selectedAlatIds = is_array($selectedAlatIds) ? $selectedAlatIds : [];
        $selectedQuantities = is_array($selectedQuantities) ? $selectedQuantities : [];
    @endphp

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Form Pengajuan Peminjaman
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Pilih alat yang ingin dipinjam dan tentukan tanggal pengembaliannya.
        </p>
    </div>

    @if(session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="font-semibold text-slate-800">Data Peminjaman</h3>
            </div>

            <div class="p-5">
                <div class="mb-6">
                    <label for="tgl_kembali_plan" class="mb-2 block text-sm font-medium text-slate-700">
                        Rencana Tanggal Kembali
                    </label>
                    <input
                        id="tgl_kembali_plan"
                        type="datetime-local"
                        name="tgl_kembali_plan"
                        min="{{ now()->format('Y-m-d\TH:i') }}"
                        value="{{ old('tgl_kembali_plan') }}"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 md:w-1/2"
                        required
                    >
                </div>

                <fieldset>
                    <legend class="mb-3 block text-sm font-medium text-slate-700">
                        Pilih Alat
                    </legend>

                    <div class="space-y-3">
                        @forelse($alats as $alat)
                            @php
                                $selectedIndex = array_search($alat->id, $selectedAlatIds);
                                $isSelected = $selectedIndex !== false;
                                $quantity = $isSelected ? ($selectedQuantities[$selectedIndex] ?? 1) : 1;
                            @endphp

                            <div class="rounded-xl border border-slate-200 p-3.5 transition hover:bg-slate-50">
                                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                    <div class="flex min-w-0 items-center gap-3">
                                        @if($alat->gambar)
                                            <img
                                                src="{{ asset('storage/' . preg_replace('#^(?:public/)?storage/#', '', $alat->gambar)) }}"
                                                alt="{{ $alat->nama_alat }}"
                                                class="h-14 w-14 shrink-0 rounded-lg border border-slate-200 bg-white object-contain p-1"
                                            >
                                        @else
                                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-xs text-slate-400">
                                                Tidak ada
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <h4 class="truncate font-semibold text-slate-800">
                                                {{ $alat->nama_alat }}
                                            </h4>
                                            <p class="text-sm text-slate-500">
                                                {{ $alat->kategori->nama_kategori ?? '-' }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-slate-500">
                                                Stok tersedia: {{ $alat->stok }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex shrink-0 items-center justify-end gap-3">
                                        <input
                                            id="alat-{{ $alat->id }}"
                                            type="checkbox"
                                            name="alat_id[]"
                                            value="{{ $alat->id }}"
                                            class="alat-checkbox h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                            data-target="jumlah-{{ $alat->id }}"
                                            @checked($isSelected)
                                        >
                                        <label for="alat-{{ $alat->id }}" class="cursor-pointer text-sm text-slate-600">
                                            Pilih
                                        </label>
                                        <input
                                            id="jumlah-{{ $alat->id }}"
                                            type="number"
                                            name="jumlah[]"
                                            min="1"
                                            max="{{ $alat->stok }}"
                                            value="{{ $quantity }}"
                                            @disabled(!$isSelected)
                                            aria-label="Jumlah {{ $alat->nama_alat }}"
                                            class="jumlah-input w-16 rounded-lg border border-slate-300 px-3 py-2 text-center focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $isSelected ? '' : 'bg-slate-100' }}"
                                        >
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-slate-200 bg-slate-50 py-10 text-center text-sm text-slate-500">
                                Tidak ada alat yang tersedia untuk dipinjam.
                            </div>
                        @endforelse
                    </div>
                </fieldset>
            </div>

            <div class="flex justify-end border-t border-slate-200 bg-slate-50 px-5 py-4">
                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                >
                    Ajukan Peminjaman
                </button>
            </div>
        </div>
    </form>

    <script>
        document.querySelectorAll('.alat-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const quantityInput = document.getElementById(this.dataset.target);

                quantityInput.disabled = !this.checked;
                quantityInput.classList.toggle('bg-slate-100', !this.checked);

                if (!this.checked) {
                    quantityInput.value = 1;
                }
            });
        });
    </script>
@endsection
