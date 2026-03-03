@extends('layouts.app')
@section('page-title', 'Retur Barang')
@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-6" x-data="konfirmasiRetur()">
        <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-md">

            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">
                    Detail Retur Barang
                </h1>
                <a href="{{ route('retur.index') }}" class="text-blue-600 hover:underline text-sm flex items-center gap-1">
                    ← Kembali
                </a>
            </div>

            {{-- Informasi Retur --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Nomor Retur</p>
                    <p class="font-semibold text-gray-800">#{{ $retur->retur_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Retur</p>
                    <p class="font-semibold text-gray-800">
                        {{ $retur->tanggal_retur?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nomor PO</p>
                    <p class="font-semibold text-gray-800">{{ $retur->po_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Kode Barang</p>
                    <p class="font-semibold text-gray-800">{{ $retur->kode_barang }}</p>
                </div>
            </div>

            {{-- Detail Barang --}}
            <div class="border-t pt-4 mt-2">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Barang</h2>
                <div class="bg-gray-50 border rounded-lg p-4">
                    <p><span class="font-medium">Nama Barang:</span> {{ $retur->barang->nama_barang ?? '-' }}</p>
                    <p><span class="font-medium">Qty Retur:</span> {{ $retur->qty_retur }}</p>
                    <p><span class="font-medium">Alasan Retur:</span> {{ $retur->alasan ?: '-' }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="border-t pt-4 mt-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Status Retur</h2>

                @if ($retur->status_retur === 'Pending')
                    <span
                        class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">Pending</span>
                @elseif ($retur->status_retur === 'Disetujui')
                    <span
                        class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Disetujui</span>
                @elseif ($retur->status_retur === 'Ditolak')
                    <span class="inline-block px-3 py-1 bg-red-700 text-white rounded-full text-sm font-medium">Ditolak</span>
                @else
                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">-</span>
                @endif
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3 mt-8">
                @if ($retur->status_retur == 'Pending')
                    <button @click="konfirmasiSesuai('{{ $retur->retur_id }}')"
                        class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 focus:ring-2 focus:ring-green-400 transition">
                        ✅ Konfirmasi Sesuai
                    </button>

                    <button @click="batalkanRetur('{{ $retur->retur_id }}')"
                        class="px-4 py-2 bg-red-600 text-white font-semibold rounded-lg shadow hover:bg-red-700 transition">
                        ❌ Batalkan Retur
                    </button>
                @endif

            </div>

        </div>
    </div>
@endsection

{{-- Script SweetAlert + Alpine --}}
@push('scripts')
    {{-- SweetAlert2 loaded globally via app.blade.php layout --}}
    <script>
        function konfirmasiRetur() {
            return {
                konfirmasiSesuai(retur_id) {
                    Swal.fire({
                        title: 'Konfirmasi Retur?',
                        text: 'Pastikan barang retur sudah selesai diperiksa.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Konfirmasi',
                        cancelButtonText: 'Batal',
                        customClass: { popup: 'swal-custom-popup' },
                    }).then(async (result) => {
                        if (result.isConfirmed) {
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
                                    customClass: { popup: 'swal-custom-popup swal-success-popup' },
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    customClass: { popup: 'swal-custom-popup' },
                                });
                            }
                        }
                    });
                },

                batalkanRetur(retur_id) {
                    Swal.fire({
                        title: 'Batalkan Retur?',
                        text: 'Retur akan ditandai sebagai Ditolak.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Batalkan',
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
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message,
                                    customClass: { popup: 'swal-custom-popup' },
                                });
                            }
                        }
                    });
                }
            }
        }

    </script>
@endpush