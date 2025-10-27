@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-lg rounded-lg p-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Purchase Order</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 border-b pb-4">
            <div>
                <p class="text-sm font-semibold text-gray-500">Nomor PO:</p>
                <p class="text-lg font-medium text-gray-900">{{ $purchaseOrder->po_id }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500">Status:</p>
                {{-- Menggunakan status kolom 'status' (bukan status_po) --}}
                <p class="text-lg font-medium text-{{ $purchaseOrder->status_po == 'Disetujui' ? 'green' : ($purchaseOrder->status_po == 'Ditolak' ? 'red' : 'yellow') }}-600">
                    {{ $purchaseOrder->status_po }}
                </p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500">Tanggal PO:</p>
                <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500">Dibuat Oleh:</p>
                <p class="text-lg text-gray-900">{{ $purchaseOrder->user->nama_lengkap ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500">Supplier:</p>
                {{-- Pastikan nama relasi 'supplier' dan kolom 'namaSupplier' diakses dengan benar --}}
                <p class="text-lg text-gray-900">{{ $purchaseOrder->Supplier->namaSupplier ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500">Total Harga:</p>
                <p class="text-xl font-bold text-indigo-600">
                    Rp{{ number_format($purchaseOrder->total_harga, 2, ',', '.') }}
                </p>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Item Pesanan</h2>
        <div class="overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Kadaluarsa Batch</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($purchaseOrder->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $detail->kode_barang }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $detail->barang->nama_barang ?? 'Barang Tidak Ditemukan' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                {{ $detail->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                {{ $detail->satuan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                Rp{{ number_format($detail->harga_satuan, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800 text-right">
                                Rp{{ number_format($detail->quantity * $detail->harga_satuan, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                @if ($detail->tgl_kadaluarsa_batch)
                                    {{ \Carbon\Carbon::parse($detail->tgl_kadaluarsa_batch)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mt-6">
            {{-- Tombol Kembali --}}
            <a href="{{ route('approval.approval_po') }}" class="w-full md:w-auto px-4 py-2 mb-4 md:mb-0 text-center text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150 shadow-md">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar PO
            </a>
            
            <div class="text-right flex space-x-3">
                {{-- Aksi Persetujuan (Hanya muncul jika statusnya 'Pending') --}}
                @if ($purchaseOrder->status_po == 'Pending')
                    {{-- Form Tolak --}}
                    <form action="{{ route('po.reject', $purchaseOrder->po_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK Purchase Order ini? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        {{-- Laravel menggunakan PUT/PATCH untuk update, tapi POST bisa digunakan untuk aksi, kita bisa simulasikan method PUT/PATCH --}}
                        @method('PUT') 
                        <button type="submit" class="px-6 py-3 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition duration-150 shadow-md transform hover:scale-105">
                            Tolak PO
                        </button>
                    </form>

                    {{-- Form Setujui --}}
                    <form action="{{ route('po.approve', $purchaseOrder->po_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI Purchase Order ini? Inventori akan ditambahkan setelah barang diterima.');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="px-6 py-3 text-sm font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 transition duration-150 shadow-md transform hover:scale-105">
                            Setujui PO
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="text-right mt-4 md:mt-0">
                <p class="text-lg font-semibold text-gray-700">TOTAL KESELURUHAN:</p>
                <p class="text-3xl font-extrabold text-indigo-700">
                    Rp{{ number_format($purchaseOrder->total_harga, 2, ',', '.') }}
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
