@extends('layouts.app')

@section('page-title', 'Tracking Kadaluarsa Barang')

@section('content')
    <div class="space-y-6">

        {{-- HEADER + SEARCH --}}
        <x-page-header title="Tracking Kadaluarsa Barang" description="Pantau masa kedaluwarsa barang berdasarkan batch stok." icon="fa-hourglass-half">
            <x-slot name="actions">
                <a href="{{ route('tracking_kadaluarsa.riwayat') }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                    <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
                    <span>Riwayat Hapus</span>
                </a>
            </x-slot>
        </x-page-header>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari
                        Barang</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kode / nama barang..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                    </div>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Cari
                </button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-red-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Batch Kadaluarsa</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PO
                                ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kode Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Barang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kadaluarsa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Sisa Hari</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok (Karton)</th>
                            @if(auth()->user()->role !== 'Staff')
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grouped = $stok->groupBy(function ($item) {
                                return $item->barang->nama_barang ?? 'Barang Dihapus';
                            });
                        @endphp

                        @forelse ($grouped as $namaBarang => $items)
                            {{-- GROUP HEADER --}}
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <td colspan="{{ auth()->user()->role !== 'Staff' ? 7 : 6 }}" class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-slate-800 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-boxes-stacked text-white text-xs"></i>
                                        </div>
                                        <span class="font-bold text-gray-800">{{ strtoupper($namaBarang) }}</span>
                                        <span
                                            class="px-2 py-0.5 bg-slate-200 text-slate-600 text-xs font-semibold rounded-full">
                                            {{ $items->count() }} batch
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            {{-- BATCH ROWS --}}
                            @foreach ($items as $s)
                                @php
                                    $expiryDate = strtotime($s->tgl_kadaluarsa);
                                    $daysLeft = floor(($expiryDate - time()) / (60 * 60 * 24));

                                    $isExpired = $daysLeft < 0;
                                    $isCritical = $daysLeft >= 0 && $daysLeft < 30;
                                    $isWarning = $daysLeft >= 30 && $daysLeft < 60;

                                    $rowBg = $isExpired ? 'bg-gray-100' : ($isCritical ? 'bg-red-50' : ($isWarning ? 'bg-amber-50' : ''));
                                @endphp

                                <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors {{ $rowBg }}">
                                    <td class="px-4 py-3 text-gray-600">{{ $s->po_id }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $s->kode_barang }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $s->barang->nama_barang ?? 'Barang Dihapus' }}
                                    </td>

                                    <td class="px-4 py-3 text-center text-gray-700">{{ date('d M Y', $expiryDate) }}</td>

                                    <td class="px-4 py-3 text-center">
                                        @if($isExpired)
                                            <span
                                                class="px-2.5 py-1 bg-gray-200 text-gray-600 text-xs font-bold rounded-full">Expired</span>
                                        @elseif($isCritical)
                                            <span
                                                class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $daysLeft }}
                                                hari</span>
                                        @elseif($isWarning)
                                            <span
                                                class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">{{ $daysLeft }}
                                                hari</span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">{{ $daysLeft }}
                                                hari</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-lg">
                                            {{ number_format($s->jumlah_stok, 2, ',', '.') }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if(auth()->user()->role !== 'Staff')
                                        <form action="{{ route('tracking_kadaluarsa.destroy', $s->id) }}" method="POST"
                                            class="inline-block deleteForm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors shadow-sm">
                                                <i class="fa-solid fa-trash text-[10px]"></i> Hapus dari Stok
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-check text-green-600 text-lg"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">Tidak ada data kadaluarsa ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Tindakan ini bersifat permanen dan tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal-custom-popup' },
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>
@endpush