@extends('layouts.app')
@section('page-title', 'Retur Barang')
@section('content')
    <!-- Container Utama dengan Shadow dan Border -->
    <div class="min-h-screen bg-gray-50 py-8 px-6" x-data="returActions()">
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">
            Manajemen Retur Barang
        </h1>
        <p class="text-base text-gray-600 border-b pb-4 mb-4">
            Memastikan dan Mengonfirmasi Retur Barang Yang Telah Diverifikasi Fisik
        </p>
        <div class=" mx-auto bg-white p-8 rounded-xl shadow-xl border border-gray-200">
            {{-- Header & Filter Controls --}}
            <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 border-b pb-4">
                <h1 class="text-2xl font-semibold text-gray-800 mb-4 md:mb-0">Daftar Barang Retur</h1>

                <form method="GET" class="flex flex-wrap items-center gap-3">
                    {{-- Input Search dengan Fokus Ring Merah --}}
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode barang / PO ID"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:outline-none shadow-sm min-w-[180px]">

                    {{-- Select Status dengan Fokus Ring Merah --}}
                    <select name="status"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 shadow-sm focus:ring-2 focus:ring-red-600 focus:border-red-600 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Disetujui" {{ $status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>

                    {{-- Tombol Filter dengan Warna Merah --}}
                    <button type="submit"
                        class="px-4 py-2 bg-red-700 text-white rounded-lg font-medium hover:bg-red-800 transition duration-150 shadow-md">
                        Filter
                    </button>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
                <table class="min-w-full text-sm text-gray-700">
                    {{-- Header Tabel Warna Merah --}}
                    <thead class="bg-red-600 text-white uppercase text-xs font-semibold tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-left">#</th>
                            <th class="py-3 px-4 text-left">PO ID</th>
                            <th class="py-3 px-4 text-left">Kode Barang</th>
                            <th class="py-3 px-4 text-left">Nama Barang</th>
                            <th class="py-3 px-4 text-center">Qty Retur (Karton)</th>
                            <th class="py-3 px-4 text-left">Alasan</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Tanggal Retur</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @forelse ($returs as $index => $retur)
                            <tr class="border-t border-gray-100 hover:bg-red-50 transition duration-100">
                                <td class="py-3 px-4">{{ $returs->firstItem() + $index }}</td>
                                <td class="py-3 px-4 font-medium text-gray-800">{{ $retur->po_id }}</td>
                                <td class="py-3 px-4">{{ $retur->kode_barang }}</td>
                                <td class="py-3 px-4">{{ $retur->barang->nama_barang ?? 'Barang Tidak Ditemukan' }}</td>
                                <td class="py-3 px-4 text-center font-bold text-red-700 bg-red-50/50">
                                    {{ number_format($retur->qty_retur, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-gray-800">
                                    {{-- Field Alasan yang Dapat Diedit --}}
                                    <div x-data="{ editing: false, alasan: '{{ $retur->alasan }}' }"
                                        @click.away="editing = false" class="relative">
                                        {{-- Teks normal --}}
                                        <div x-show="!editing" class="cursor-pointer hover:bg-gray-100 p-1 rounded transition"
                                            @click="editing = true">
                                            <span x-text="alasan || 'Klik untuk isi alasan'"></span>
                                        </div>

                                        {{-- Input edit dengan Fokus Ring Merah --}}
                                        <div x-show="editing" class="flex gap-2">
                                            <input type="text" x-model="alasan"
                                                class="border border-gray-300 rounded px-2 py-1 w-full text-sm focus:ring focus:ring-red-200 focus:border-red-400"
                                                @keydown.enter.prevent="
                                                                        fetch('{{ route('retur.updateAlasan', $retur->retur_id) }}', {
                                                                            method: 'PATCH',
                                                                            headers: { 
                                                                                'Content-Type': 'application/json',
                                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                            },
                                                                            body: JSON.stringify({ alasan })
                                                                        })
                                                                        .then(res => res.json())
                                                                        .then(data => { 
                                                                            if(data.success){ 
                                                                                editing = false 
                                                                            } else {
                                                                                console.error('Gagal update alasan:', data.message)
                                                                            }
                                                                        })
                                                                    ">
                                            <button @click="editing = false"
                                                class="text-gray-500 hover:text-red-700 text-sm whitespace-nowrap">Batal</button>
                                        </div>
                                    </div>
                                </td>

                                {{-- Badge Status --}}
                                <td class="py-3 px-4 text-center">
                                    @if ($retur->status_retur === 'Pending')
                                        <span
                                            class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold shadow-sm">Pending</span>
                                    @elseif ($retur->status_retur === 'Disetujui')
                                        <span
                                            class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold shadow-sm">Disetujui</span>
                                    @elseif ($retur->status_retur === 'Ditolak')
                                        <span
                                            class="inline-block px-3 py-1 bg-red-700 text-white rounded-full text-xs font-semibold shadow-sm">Ditolak</span>
                                    @else
                                        <span
                                            class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold shadow-sm">{{ $retur->status_retur }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div x-data="{ editing: false, tg: '{{ $retur->tanggal_retur }}' }"
                                        @click.away="editing = false" class="relative">

                                        {{-- tampilan normal --}}
                                        <div x-show="!editing" class="cursor-pointer hover:bg-gray-100 p-1 rounded"
                                            @click="editing = true">
                                            {{ $retur->tanggal_retur?->format('d/m/Y') }}
                                        </div>

                                        {{-- input date --}}
                                        <div x-show="editing" class="flex gap-2">
                                            <input type="date" x-model="tg"
                                                class="border border-gray-300 rounded px-2 py-1 w-full text-sm focus:ring focus:ring-red-200 focus:border-red-400"
                                                @keydown.enter.prevent="
                                                    fetch('{{ route('retur.updateTanggal', $retur->retur_id) }}', {
                                                        method: 'PATCH',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({ tanggal_retur: tg })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if(data.success) {
                                                            editing = false;
                                                            window.location.reload();
                                                        }
                                                    })
                                            ">
                                            <button @click="editing = false"
                                                class="text-gray-500 hover:text-red-700 text-sm">Batal</button>
                                        </div>

                                    </div>
                                </td>


                                {{-- Tombol Aksi Detail Merah --}}
                                @if ($retur->status_retur === 'Pending')
                                    <td class="py-3 px-4 text-center flex gap-2 justify-center">
                                        {{-- SETUJUI --}}
                                        <button @click="konfirmasiSesuai('{{ $retur->retur_id }}')"
                                            class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs font-medium">
                                            Setujui
                                        </button>

                                        {{-- TOLAK --}}
                                        <button @click="batalkanRetur('{{ $retur->retur_id }}')"
                                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-medium">
                                            Tolak
                                        </button>

                                    </td>
                                @else
                                    <td class="text-center">
                                        <span class="text-gray-400 text-xs justify-center italic">Tidak ada aksi</span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-gray-500 bg-gray-50 text-base font-medium">
                                    <svg class="w-8 h-8 inline-block mr-2 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    Tidak ada data retur barang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $returs->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- SweetAlert2 loaded globally via app.blade.php layout --}}

        <script>
            function returActions() {
                return {

                    // ======================= SETUJUI =======================
                    konfirmasiSesuai(retur_id) {
                        Swal.fire({
                            title: 'Setujui Retur?',
                            text: 'Retur akan disetujui.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Setujui',
                            cancelButtonText: 'Batal',
                            customClass: { popup: 'swal-custom-popup' },
                        }).then(async (result) => {
                            if (result.isConfirmed) {

                                const res = await fetch(`/retur-barang/${retur_id}/konfirmasi`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                });

                                const data = await res.json();

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        customClass: { popup: 'swal-custom-popup swal-success-popup' },
                                    }).then(() => {
                                        window.location.reload();
                                    });

                                } else {
                                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, customClass: { popup: 'swal-custom-popup' } });
                                }
                            }
                        });
                    },

                    // ======================= TOLAK =======================
                    batalkanRetur(retur_id) {

                        Swal.fire({
                            title: 'Tolak Retur?',
                            text: 'Retur akan ditandai sebagai Ditolak.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Tolak',
                            cancelButtonText: 'Batal',
                            customClass: { popup: 'swal-custom-popup' },
                        }).then(async (result) => {

                            if (result.isConfirmed) {
                                const res = await fetch(`/retur-barang/${retur_id}/batal`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    }
                                });

                                const data = await res.json();

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        customClass: { popup: 'swal-custom-popup swal-success-popup' },
                                    }).then(() => window.location.reload());
                                }
                                else {
                                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message, customClass: { popup: 'swal-custom-popup' } });
                                }
                            }

                        });
                    }

                }
            }
        </script>
    @endpush

@endsection