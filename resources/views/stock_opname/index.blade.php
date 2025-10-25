@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-4 sm:mb-0">
            Daftar Sesi Stock Opname
        </h1>
        
        <!-- Tombol untuk memulai Stock Opname Baru -->
        <a href="{{ route('stock_opname.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out">
            <i class="fa-solid fa-plus-circle mr-2"></i>
            Mulai Stock Opname Baru
        </a>
    </div>

    <!-- Kotak Notifikasi -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    <!-- Tabel Daftar Opname -->
    <div class="bg-white shadow-xl rounded-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID Sesi
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal Opname
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Petugas
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total Selisih
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{-- Loop data Stock Opname --}}
                @forelse ($stockOpnames as $opname)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #{{ $opname->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($opname->tanggal_opname)->isoFormat('D MMMM YYYY') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $opname->user->name ?? 'N/A' }} {{-- Asumsi ada relasi ke model User --}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm @if($opname->total_discrepancy > 0) text-red-600 font-bold @elseif($opname->total_discrepancy < 0) text-green-600 font-bold @else text-gray-500 @endif">
                        {{ $opname->total_discrepancy }} item
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @php
                            $badgeClass = [
                                'Selesai' => 'bg-green-100 text-green-800',
                                'Draft' => 'bg-yellow-100 text-yellow-800',
                                'Ditolak' => 'bg-red-100 text-red-800',
                            ][$opname->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                            {{ $opname->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-center">
                        {{-- Tombol View/Lanjutkan --}}
                        <a href="{{ route('stock_opname.show', $opname->id) }}" 
                           class="text-indigo-600 hover:text-indigo-900 mx-2" title="Lihat Detail/Lanjutkan">
                           <i class="fa-solid fa-file-invoice cursor-pointer text-indigo-500"></i>
                        </a>

                        {{-- Tombol Delete (Hanya jika status Draft) --}}
                        @if ($opname->status === 'Draft')
                            <form action="{{ route('stock_opname.destroy', $opname->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi Opname ini? Data akan hilang permanen.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 mx-2" title="Hapus Draft">
                                    <i class="fa-solid fa-trash-can cursor-pointer"></i>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 text-base">
                        Belum ada data Stock Opname yang tercatat. Silakan mulai sesi baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tampilkan Link Paginasi --}}
    <div class="mt-4">
        {{-- Asumsi $stockOpnames adalah objek Paginator --}}
        {{ $stockOpnames->links() }} 
    </div>
</div>

@endsection
