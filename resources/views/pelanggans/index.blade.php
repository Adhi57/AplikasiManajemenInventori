@extends('layouts.app')
@section('page-title', 'Data Master / Pelanggan')
@section('content')

<!-- Card Utama Container -->
<div class="p-4 sm:p-6 bg-gray-50 rounded-xl shadow-inner min-h-[80vh]">

    <div class="grid grid-cols-12 gap-4 items-center mb-6">
        <!-- Kolom Pencarian -->
        <div class="relative col-span-12 md:col-span-4 text-gray-600">
            <input
                type="search"
                id="search-input"
                placeholder="Cari Kode atau Nama Pelanggan..."
                class="w-full h-10 px-5 text-sm border border-gray-300 outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 rounded-xl transition duration-150 shadow-sm"
                onkeyup="filterTable()"
            >
            <i class="fa-solid fa-magnifying-glass absolute right-0 top-0 mt-3 mr-4 text-gray-400"></i>
        </div>

        <!-- Kolom Tambah Pelanggan -->
        <div class="col-span-12 md:col-span-8 flex justify-end">
            <a href="{{ route('pelanggans.create') }}"
                class="bg-red-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg hover:bg-red-800 transition duration-150 ease-in-out flex items-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                Tambah Pelanggan Baru
            </a>
        </div>
    </div>


    <!-- TABEL -->
    <div class="my-3">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-sm font-medium" role="alert">
            {{ session('success') }}
        </div>
        @endif
        
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl border border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Daftar Pelanggan</h2>

                <div class="overflow-x-auto">
                    <table id="pelanggan-table" class="w-full text-sm text-left rtl:text-right text-gray-700">
                        <thead class="text-xs text-white uppercase bg-red-700 border-b-4 border-red-500">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center rounded-tl-xl">Kode Pelanggan</th>
                                <th scope="col" class="px-6 py-3">Nama Pelanggan</th>
                                <th scope="col" class="px-6 py-3">Alamat</th>
                                <th scope="col" class="px-6 py-3">NPWP</th>
                                <th scope="col" class="px-6 py-3">PIC</th>
                                <th scope="col" class="px-6 py-3">Kategori Pelanggan</th>
                                <th scope="col" class="px-6 py-3">Tipe Harga</th>
                                <th scope="col" class="px-6 py-3 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pelanggans as $pelanggan)
                            <tr class=" text-left text-xs border-b even:bg-red-50 odd:bg-white hover:bg-red-100 transition duration-100">
                                <td scope="row" class="px-6 py-2 font-semibold text-center text-gray-900">{{ $pelanggan->pelanggan_id }}</td>
                                <td class="px-6 py-2">{{ $pelanggan->nama_pelanggan }}</td>
                                <td class="px-6 py-2">{{ $pelanggan->alamat}}</td>
                                <td class="px-6 py-2">{{ $pelanggan->NPWP }}</td>
                                <td class="px-6 py-2">{{ $pelanggan->PIC }}</td>
                                <td class="px-6 py-2">{{ $pelanggan->kategori_pelanggan->kategori_pelanggan ?? '-' }}</td>
                                
                                <td class="px-6 py-2">
                                    <span class="font-medium px-2 py-0.5 rounded-full text-white text-[10px] uppercase tracking-wider
                                        @if(isset($pelanggan->tipe_harga) && $pelanggan->tipe_harga == 'Eceran') bg-blue-600 shadow-md shadow-blue-300
                                        @elseif(isset($pelanggan->tipe_harga) && $pelanggan->tipe_harga == 'Grosir') bg-green-600 shadow-md shadow-green-300
                                        @else bg-gray-500 shadow-md shadow-gray-300 @endif">
                                        {{ $pelanggan->tipe_harga ?? 'Umum' }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-2 text-center whitespace-nowrap">
                                    <!-- View -->
                                    <a href="{{ route('pelanggans.show', $pelanggan->pelanggan_id) }}"
                                      class="inline-block text-gray-500 hover:text-blue-700 transition duration-150 p-1">
                                      <i class="fa-solid fa-eye cursor-pointer text-sm"></i>
                                    </a>
        
                                    <!-- EDIT -->
                                    <a href="{{ route('pelanggans.edit', $pelanggan->pelanggan_id) }}"
                                      class="inline-block text-gray-500 hover:text-amber-600 transition duration-150 p-1">
                                      <i class="fa-solid fa-pen-to-square cursor-pointer text-sm"></i>
                                    </a>
        
                                    <!-- Delete -->
                                    <form action="{{ route('pelanggans.destroy', $pelanggan->pelanggan_id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Yakin ingin menghapus data pelanggan {{ $pelanggan->nama_pelanggan }}? Aksi ini tidak dapat dibatalkan.');">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="text-gray-500 hover:text-red-700 transition duration-150 p-1" title="Hapus">
                                        <i class="fa-solid fa-trash-can cursor-pointer text-sm"></i>
                                      </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Tambahkan Pagiasi di sini (jika menggunakan fitur Paginate dari Laravel) --}}
            {{-- <div class="p-4 border-t bg-gray-50">
                {{ $pelanggans->links() }}
            </div> --}}

        </div>

    </div>
</div>

<script>
    /**
     * Fungsi untuk memfilter baris tabel berdasarkan input pencarian.
     * Pencarian dilakukan di sisi klien (browser) untuk Kode Pelanggan (kolom 0) dan Nama Pelanggan (kolom 1).
     */
    function filterTable() {
        // Ambil nilai input dan konversi ke huruf kecil
        const input = document.getElementById("search-input");
        const filter = input.value.toLowerCase();
        
        // Ambil tabel dan semua baris data (skip header/thead)
        const table = document.getElementById("pelanggan-table");
        const tr = table.getElementsByTagName("tr");

        // Loop melalui semua baris tabel (mulai dari index 1 untuk melewati header)
        for (let i = 1; i < tr.length; i++) {
            // Ambil kolom Kode Pelanggan (index 0) dan Nama Pelanggan (index 1)
            const tdKode = tr[i].getElementsByTagName("td")[0];
            const tdNama = tr[i].getElementsByTagName("td")[1];
            
            let match = false;

            // Pastikan elemen kolom ada
            if (tdKode) {
                const kodeText = tdKode.textContent || tdKode.innerText;
                if (kodeText.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                }
            }

            if (!match && tdNama) {
                const namaText = tdNama.textContent || tdNama.innerText;
                if (namaText.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                }
            }

            // Tampilkan atau sembunyikan baris berdasarkan hasil pencarian
            if (match) {
                tr[i].style.display = ""; // Tampilkan baris
            } else {
                tr[i].style.display = "none"; // Sembunyikan baris
            }
        }
    }
</script>

@endsection