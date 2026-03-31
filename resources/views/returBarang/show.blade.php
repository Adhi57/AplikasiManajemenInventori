@extends('layouts.app')
@section('page-title', 'Detail Retur Barang')
@section('content')

    <div class="min-h-screen bg-gray-50/50 py-6 px-2 md:px-6" x-data="konfirmasiRetur()">

        {{-- ========================================= --}}
        {{-- BREADCRUMB & HEADER --}}
        {{-- ========================================= --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                    <a href="{{ route('retur.index') }}" class="hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-rotate-left text-xs mr-1"></i>Retur Barang
                    </a>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    <span class="text-gray-600 font-medium">Detail #{{ $retur->retur_id }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Detail Retur Barang
                </h1>
            </div>

            <a href="{{ route('retur.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="max-w-4xl mx-auto space-y-5">

            {{-- ========================================= --}}
            {{-- STATUS BANNER --}}
            {{-- ========================================= --}}
            @if ($retur->status_retur === 'Pending')
                <div
                    class="relative overflow-hidden bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-5 shadow-lg">
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-clock text-white text-2xl animate-pulse"></i>
                        </div>
                        <div class="text-white">
                            <h3 class="font-bold text-lg">Menunggu Persetujuan</h3>
                            <p class="text-white/80 text-sm">Retur ini masih menunggu konfirmasi dari admin. Silakan
                                periksa dan ambil tindakan.</p>
                        </div>
                    </div>
                </div>
            @elseif ($retur->status_retur === 'Disetujui')
                <div
                    class="relative overflow-hidden bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-2xl p-5 shadow-lg">
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-circle-check text-white text-2xl"></i>
                        </div>
                        <div class="text-white">
                            <h3 class="font-bold text-lg">Retur Disetujui</h3>
                            <p class="text-white/80 text-sm">Retur ini telah disetujui dan diproses. Barang dikembalikan
                                ke supplier.</p>
                        </div>
                    </div>
                </div>
            @elseif ($retur->status_retur === 'Ditolak')
                <div
                    class="relative overflow-hidden bg-gradient-to-r from-red-600 to-red-700 rounded-2xl p-5 shadow-lg">
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="relative z-10 flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-circle-xmark text-white text-2xl"></i>
                        </div>
                        <div class="text-white">
                            <h3 class="font-bold text-lg">Retur Ditolak</h3>
                            <p class="text-white/80 text-sm">Retur ini telah ditolak. Barang tidak dikembalikan ke
                                supplier.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ========================================= --}}
            {{-- INFO CARDS GRID --}}
            {{-- ========================================= --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Informasi Retur --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-2 h-5 bg-red-600 rounded-full"></div>
                        <h2 class="font-semibold text-gray-800 text-sm">Informasi Retur</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-hashtag text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Nomor Retur</p>
                                <p class="font-bold text-gray-800">#{{ $retur->retur_id }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-file-invoice text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Nomor PO</p>
                                <p class="font-bold text-gray-800">{{ $retur->po_id }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                            <div
                                class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-regular fa-calendar text-violet-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Tanggal Retur
                                </p>
                                <p class="font-bold text-gray-800">
                                    {{ $retur->tanggal_retur?->format('d F Y, H:i') ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Barang --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-2 h-5 bg-blue-600 rounded-full"></div>
                        <h2 class="font-semibold text-gray-800 text-sm">Informasi Barang</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                            <div
                                class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-barcode text-amber-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Kode Barang</p>
                                <p class="font-bold text-gray-800">{{ $retur->kode_barang }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                            <div
                                class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-box-open text-emerald-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Nama Barang</p>
                                <p class="font-bold text-gray-800">{{ $retur->barang->nama_barang ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 p-3 bg-red-50 rounded-xl border border-red-100">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-arrow-rotate-left text-red-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider font-medium">Qty Retur</p>
                                <p class="font-bold text-red-700 text-lg">
                                    {{ number_format($retur->qty_retur, 2, ',', '.') }}
                                    <span class="text-sm font-normal text-gray-400">Karton</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================= --}}
            {{-- ALASAN RETUR --}}
            {{-- ========================================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-5 bg-amber-500 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800 text-sm">Alasan Retur</h2>
                </div>
                <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-comment-dots text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-gray-700 text-sm leading-relaxed">
                                {{ $retur->alasan ?: 'Belum ada alasan yang diberikan.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================= --}}
            {{-- STATUS TIMELINE --}}
            {{-- ========================================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-2 h-5 bg-slate-700 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800 text-sm">Timeline Status</h2>
                </div>

                <div class="flex items-center gap-0">
                    {{-- Step 1: Dibuat --}}
                    <div class="flex flex-col items-center flex-1">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-100 text-blue-600 border-2 border-blue-500">
                            <i class="fa-solid fa-file-circle-plus text-lg"></i>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 mt-2">Dibuat</p>
                        <p class="text-[10px] text-gray-400">
                            {{ $retur->created_at?->format('d/m/Y') ?? '-' }}
                        </p>
                    </div>

                    {{-- Connector --}}
                    <div
                        class="flex-1 h-1 rounded-full mx-1 {{ in_array($retur->status_retur, ['Pending', 'Disetujui', 'Ditolak']) ? 'bg-blue-400' : 'bg-gray-200' }}">
                    </div>

                    {{-- Step 2: Pending --}}
                    <div class="flex flex-col items-center flex-1">
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center {{ in_array($retur->status_retur, ['Pending', 'Disetujui', 'Ditolak']) ? 'bg-amber-100 text-amber-600 border-2 border-amber-500' : 'bg-gray-100 text-gray-400 border-2 border-gray-300' }}">
                            <i class="fa-solid fa-clock text-lg"></i>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 mt-2">Pending</p>
                        <p class="text-[10px] text-gray-400">
                            {{ $retur->status_retur === 'Pending' ? 'Saat ini' : 'Selesai' }}
                        </p>
                    </div>

                    {{-- Connector --}}
                    <div
                        class="flex-1 h-1 rounded-full mx-1 {{ in_array($retur->status_retur, ['Disetujui', 'Ditolak']) ? ($retur->status_retur === 'Disetujui' ? 'bg-emerald-400' : 'bg-red-400') : 'bg-gray-200' }}">
                    </div>

                    {{-- Step 3: Final --}}
                    <div class="flex flex-col items-center flex-1">
                        @if ($retur->status_retur === 'Disetujui')
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center bg-emerald-100 text-emerald-600 border-2 border-emerald-500">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold text-emerald-700 mt-2">Disetujui</p>
                            <p class="text-[10px] text-gray-400">
                                {{ $retur->updated_at?->format('d/m/Y') ?? '-' }}
                            </p>
                        @elseif ($retur->status_retur === 'Ditolak')
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center bg-red-100 text-red-600 border-2 border-red-500">
                                <i class="fa-solid fa-circle-xmark text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold text-red-700 mt-2">Ditolak</p>
                            <p class="text-[10px] text-gray-400">
                                {{ $retur->updated_at?->format('d/m/Y') ?? '-' }}
                            </p>
                        @else
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center bg-gray-100 text-gray-400 border-2 border-gray-300 border-dashed">
                                <i class="fa-solid fa-question text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 mt-2">Keputusan</p>
                            <p class="text-[10px] text-gray-400">Belum diproses</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ========================================= --}}
            {{-- ACTION BUTTONS --}}
            {{-- ========================================= --}}
            @if ($retur->status_retur == 'Pending')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-2 h-5 bg-emerald-500 rounded-full"></div>
                        <h2 class="font-semibold text-gray-800 text-sm">Tindakan</h2>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button @click="konfirmasiSesuai('{{ $retur->retur_id }}')"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:bg-emerald-700 hover:shadow-xl focus:ring-4 focus:ring-emerald-200 transition-all duration-200 active:scale-[0.98]">
                            <i class="fa-solid fa-circle-check"></i> Setujui Retur
                        </button>

                        <button @click="batalkanRetur('{{ $retur->retur_id }}')"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-red-600 text-white font-semibold rounded-xl shadow-lg hover:bg-red-700 hover:shadow-xl focus:ring-4 focus:ring-red-200 transition-all duration-200 active:scale-[0.98]">
                            <i class="fa-solid fa-circle-xmark"></i> Tolak Retur
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

{{-- Script SweetAlert + Alpine --}}
@push('scripts')
    <script>
        function konfirmasiRetur() {
            return {
                konfirmasiSesuai(retur_id) {
                    Swal.fire({
                        title: 'Setujui Retur?',
                        html: '<p class="text-sm text-gray-500">Pastikan barang sudah diperiksa secara fisik sebelum menyetujui retur ini.</p>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Ya, Setujui',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'swal-custom-popup'
                        },
                        reverseButtons: true,
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                html: '<p class="text-sm text-gray-500">Menyetujui retur barang</p>',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'swal-custom-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });

                            const res = await fetch(`/retur-barang/${retur_id}/konfirmasi`, {
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
                                    customClass: {
                                        popup: 'swal-custom-popup swal-success-popup'
                                    },
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    customClass: {
                                        popup: 'swal-custom-popup'
                                    },
                                });
                            }
                        }
                    });
                },

                batalkanRetur(retur_id) {
                    Swal.fire({
                        title: 'Tolak Retur?',
                        html: '<p class="text-sm text-gray-500">Retur akan ditandai sebagai <strong class="text-red-600">Ditolak</strong>. Tindakan ini tidak dapat dibatalkan.</p>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa-solid fa-xmark mr-1"></i> Ya, Tolak',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'swal-custom-popup'
                        },
                        reverseButtons: true,
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                html: '<p class="text-sm text-gray-500">Menolak retur barang</p>',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'swal-custom-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });

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
                                    customClass: {
                                        popup: 'swal-custom-popup swal-success-popup'
                                    },
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    customClass: {
                                        popup: 'swal-custom-popup'
                                    },
                                });
                            }
                        }
                    });
                }
            }
        }
    </script>
@endpush