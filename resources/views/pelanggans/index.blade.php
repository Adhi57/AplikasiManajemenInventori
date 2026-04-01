@extends('layouts.app')
@section('page-title', 'Data Master / Pelanggan')
@section('content')

    <x-page-header title="Data Pelanggan" description="Kelola informasi pelanggan dan data kontak" icon="fa-users">
    <x-slot name="actions">
        <a href="{{ route('pelanggans.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Pelanggan Baru</span>
        </a>
    </x-slot>
</x-page-header>

    {{-- Search --}}
    <div class="mb-6">
        <div class="relative max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="search" id="search-input" placeholder="Cari kode atau nama pelanggan..." onkeyup="filterTable()"
                class="w-full h-10 pl-9 pr-4 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm">
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="mb-4 bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-emerald-50 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="text-sm font-medium text-emerald-700">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-table-list text-red-500"></i>
                Daftar Pelanggan
            </h3>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto">
                <table id="pelanggan-table" class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-white uppercase bg-red-800">
                        <tr>
                            <th class="px-5 py-3 text-center rounded-tl-xl">Kode</th>
                            <th class="px-5 py-3">Nama Pelanggan</th>
                            <th class="px-5 py-3">Alamat</th>
                            <th class="px-5 py-3">NPWP</th>
                            <th class="px-5 py-3">PIC</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Tipe Harga</th>
                            <th class="px-5 py-3 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggans as $pelanggan)
                            <tr
                                class="text-xs border-b border-gray-100 even:bg-gray-50/50 hover:bg-red-50 transition duration-100">
                                <td class="px-5 py-2.5 font-semibold text-center text-gray-900">{{ $pelanggan->pelanggan_id }}
                                </td>
                                <td class="px-5 py-2.5 font-medium">{{ $pelanggan->nama_pelanggan }}</td>
                                <td class="px-5 py-2.5">{{ $pelanggan->alamat }}</td>
                                <td class="px-5 py-2.5 font-mono text-gray-600">{{ $pelanggan->NPWP }}</td>
                                <td class="px-5 py-2.5">{{ $pelanggan->PIC }}</td>
                                <td class="px-5 py-2.5">{{ $pelanggan->kategori_pelanggan->kategori_pelanggan ?? '-' }}</td>
                                <td class="px-5 py-2.5">
                                    @php
                                        $tipeBadge = match ($pelanggan->tipe_harga ?? '') {
                                            'Eceran' => 'bg-blue-100 text-blue-700',
                                            'Grosir' => 'bg-emerald-100 text-emerald-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $tipeBadge }}">
                                        {{ $pelanggan->tipe_harga ?? 'Umum' }}
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 text-center whitespace-nowrap">
                                    <a href="{{ route('pelanggans.show', $pelanggan->pelanggan_id) }}"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                        title="Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('pelanggans.edit', $pelanggan->pelanggan_id) }}"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                        title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('pelanggans.destroy', $pelanggan->pelanggan_id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus data pelanggan {{ $pelanggan->nama_pelanggan }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                            title="Hapus">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function filterTable() {
                const input = document.getElementById("search-input");
                const filter = input.value.toLowerCase();
                const table = document.getElementById("pelanggan-table");
                const tr = table.getElementsByTagName("tr");

                for (let i = 1; i < tr.length; i++) {
                    const tdKode = tr[i].getElementsByTagName("td")[0];
                    const tdNama = tr[i].getElementsByTagName("td")[1];
                    let match = false;

                    if (tdKode) {
                        const kodeText = tdKode.textContent || tdKode.innerText;
                        if (kodeText.toLowerCase().indexOf(filter) > -1) match = true;
                    }
                    if (!match && tdNama) {
                        const namaText = tdNama.textContent || tdNama.innerText;
                        if (namaText.toLowerCase().indexOf(filter) > -1) match = true;
                    }

                    tr[i].style.display = match ? "" : "none";
                }
            }
        </script>
    @endpush

@endsection