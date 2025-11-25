@extends('layouts.app')

@section('page-title', 'Detail Surat PO')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8">

        {{-- Header --}}
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">PURCHASE ORDER</h1>
                    <p class="text-sm text-gray-500">Nomor: {{ $purchaseOrder->po_id }}</p>
                    <p class="text-sm text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}</p>
                    <p class="text-sm text-gray-500">Status:
                        <span class="font-semibold text-{{ $purchaseOrder->status_po == 'Disetujui' ? 'green' : ($purchaseOrder->status_po == 'Ditolak' ? 'red' : 'yellow') }}-600">
                            {{ $purchaseOrder->status_po }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="text-right text-sm text-gray-600">
                <p><strong>Ref:</strong> {{ $purchaseOrder->po_id }}</p>
                <p><strong>Status:</strong> {{ $purchaseOrder->status_po }}</p>
            </div>
        </div>

        {{-- Informasi Perusahaan & Supplier --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <div>
                <h2 class="font-semibold text-gray-700 border-b pb-1 mb-2">Info Perusahaan</h2>
                <p class="font-bold text-gray-800">CV. Berkah Jaya Lumintu</p>
                <p class="text-sm text-gray-600">Wonokerso I RT 02/02, Wonokerso, Tembarak</p>
                <p class="text-sm text-gray-600 mt-2">Telp: 08123456789<br>Email: berkahjayalumintu@gmail.com</p>
            </div>
            <div>
                <h2 class="font-semibold text-gray-700 border-b pb-1 mb-2">Order Ke</h2>
                <p class="font-bold text-gray-800">{{ $purchaseOrder->Supplier->namaSupplier ?? '-' }}</p>
                <p class="text-sm text-gray-600">
                    {{ $purchaseOrder->Supplier->alamatSupplier ?? 'Alamat tidak tersedia' }}
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Telp: {{ $purchaseOrder->Supplier->noTelepon ?? '-' }}<br>
                    Email: {{ $purchaseOrder->Supplier->email ?? '-' }}
                </p>
            </div>
        </div>

        {{-- Tabel Item --}}
        <table class="w-full border-collapse mb-6">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="border p-2 text-left">Produk</th>
                    <th class="border p-2 text-center">Kuantitas</th>
                    <th class="border p-2 text-right">Harga</th>
                    <th class="border p-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseOrder->details as $detail)
                <tr class="hover:bg-gray-50">
                    <td class="border p-2">
                        <p class="font-medium text-gray-800">
                            {{ $detail->barang->nama_barang ?? 'Barang Tidak Ditemukan' }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $detail->kode_barang }}</p>
                    </td>
                    <td class="border p-2 text-center">{{ $detail->quantity }} {{ $detail->satuan }}</td>
                    <td class="border p-2 text-right">Rp{{ number_format($detail->harga_satuan, 2, ',', '.') }}</td>
                    <td class="border p-2 text-right font-semibold text-gray-800">
                        Rp{{ number_format($detail->quantity * $detail->harga_satuan, 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Rincian Total --}}
        @php
        $subtotal = $purchaseOrder->details->sum(fn($d) => $d->quantity * $d->harga_satuan);
        $pajak = $subtotal * 0.11;
        $total = $subtotal + $pajak;
        @endphp

        <div class="flex justify-end mb-6">
            <table class="text-sm text-gray-700 w-1/2">
                <tr>
                    <td class="p-2 border-t border-gray-300">Subtotal</td>
                    <td class="p-2 border-t border-gray-300 text-right">Rp{{ number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="p-2 border-t border-gray-300">Pajak (11%)</td>
                    <td class="p-2 border-t border-gray-300 text-right">Rp{{ number_format($pajak, 2, ',', '.') }}</td>
                </tr>
                <tr class="font-bold bg-gray-50">
                    <td class="p-2 border-t border-gray-300">Jumlah Total</td>
                    <td class="p-2 border-t border-gray-300 text-right">Rp{{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="text-sm text-gray-600 mt-8 border-t pt-4">
            <p><strong>Syarat & Ketentuan:</strong></p>
            <p>Pembayaran dilakukan dalam waktu 30 hari setelah tanggal PO.</p>
            <p class="mt-4 text-right font-semibold text-gray-700">
                {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}
            </p>
            <p class="text-right font-medium">Bank Bersama</p>
        </div>
        <div class="flex flex-col md:flex-row justify-between items-center mt-6">
            {{-- Tombol Kembali --}}
            <a href="{{ route('approval.approval_po') }}" class="w-full md:w-auto px-4 py-2 mb-4 md:mb-0 text-center text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150 shadow-md"> <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Surat PO </a>
            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-3 mt-6"> @if ($purchaseOrder->status_po === 'Pending')
                {{-- Tombol Tolak --}}
                <form action="{{ route('po.reject', $purchaseOrder->po_id) }}" method="POST" id="rejectForm"> @csrf @method('PUT') <button type="button" onclick="confirmReject()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow"> Tolak PO </button> </form>
                {{-- Tombol Setujui --}}
                <form action="{{ route('po.approve', $purchaseOrder->po_id) }}" method="POST" id="approveForm"> @csrf @method('PUT') <button type="button" onclick="confirmApprove()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow"> Konfirmasi Sesuai </button> </form> @endif
            </div>
            {{-- SweetAlert --}}
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                function confirmApprove() {
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: "Apakah Anda yakin ingin menyetujui PO ini?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Setujui'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('approveForm').submit();
                        }
                    });
                }

                function confirmReject() {
                    Swal.fire({
                        title: 'Tolak PO?',
                        text: "Apakah Anda yakin ingin menolak PO ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Tolak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('rejectForm').submit();
                        }
                    });
                } // Tampilkan notifikasi session @if(session('success')) Swal.fire('Berhasil!', '{{ session(' success ') }}', 'success'); @elseif(session('error')) Swal.fire('Gagal!', '{{ session(' error ') }}', 'error'); @elseif(session('warning')) Swal.fire('Perhatian', '{{ session(' warning ') }}', 'warning'); @endif 
            </script>
        </div>
    </div>
    @endsection