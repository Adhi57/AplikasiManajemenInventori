@extends('layouts.app')

@section('page-title', 'Detail Surat PO')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-12">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">PURCHASE ORDER</h1>
                    <p class="text-sm text-gray-500">Nomor: {{ $purchaseOrder->po_id }}</p>
                    <p class="text-sm text-gray-500">
                        Tanggal: {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Status:
                        <span class="font-semibold
                            {{ $purchaseOrder->status_po === 'Disetujui' ? 'text-green-600' :
                               ($purchaseOrder->status_po === 'Ditolak' ? 'text-red-600' :
                               ($purchaseOrder->status_po === 'Diterima' ? 'text-blue-600' : 'text-yellow-600')) }}">
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

        {{-- ================= INFO PERUSAHAAN & SUPPLIER ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <div>
                <h2 class="font-semibold text-gray-700 border-b pb-1 mb-2">Info Perusahaan</h2>
                <p class="font-bold text-gray-800">CV. Berkah Jaya Lumintu</p>
                <p class="text-sm text-gray-600">
                    Wonokerso I RT 02/02, Wonokerso, Tembarak
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Telp: 08123456789<br>
                    Email: berkahjayalumintu@gmail.com
                </p>
            </div>

            <div>
                <h2 class="font-semibold text-gray-700 border-b pb-1 mb-2">Order Ke</h2>
                <p class="font-bold text-gray-800">
                    {{ $purchaseOrder->supplier->namaSupplier ?? '-' }}
                </p>
                <p class="text-sm text-gray-600">
                    {{ $purchaseOrder->supplier->alamatSupplier ?? 'Alamat tidak tersedia' }}
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Telp: {{ $purchaseOrder->supplier->noTelepon ?? '-' }}<br>
                    Email: {{ $purchaseOrder->supplier->email ?? '-' }}
                </p>
            </div>
        </div>

        {{-- ================= TABEL ITEM ================= --}}
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
                    <td class="border p-2 text-center">
                        {{ $detail->quantity }} {{ $detail->satuan }}
                    </td>
                    <td class="border p-2 text-right">
                        Rp{{ number_format($detail->harga_satuan, 2, ',', '.') }}
                    </td>
                    <td class="border p-2 text-right font-semibold">
                        Rp{{ number_format($detail->quantity * $detail->harga_satuan, 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ================= TOTAL ================= --}}
        @php
            $subtotal = $purchaseOrder->details->sum(fn($d) => $d->quantity * $d->harga_satuan);
            $pajak = $subtotal * 0.11;
            $total = $subtotal + $pajak;
        @endphp

        <div class="flex justify-end mb-6">
            <table class="text-sm text-gray-700 w-1/2">
                <tr>
                    <td class="p-2 border-t">Subtotal</td>
                    <td class="p-2 border-t text-right">
                        Rp{{ number_format($subtotal, 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="p-2 border-t">Pajak (11%)</td>
                    <td class="p-2 border-t text-right">
                        Rp{{ number_format($pajak, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="font-bold bg-gray-50">
                    <td class="p-2 border-t">Jumlah Total</td>
                    <td class="p-2 border-t text-right">
                        Rp{{ number_format($total, 2, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="text-sm text-gray-600 border-t pt-4">
            <p><strong>Syarat & Ketentuan:</strong></p>
            <p>Pembayaran dilakukan maksimal 30 hari setelah tanggal PO.</p>

            <div class="text-right mt-6">
                <p class="font-semibold text-gray-700">
                    {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}
                </p>
                <p class="font-medium">Bank Bersama</p>
            </div>
        </div>

        {{-- ================= ACTION BUTTON ================= --}}
        <div class="flex flex-col md:flex-row justify-between items-center mt-6">

            <a href="{{ route('approval.approval_po') }}"
               class="px-4 py-2 text-sm bg-gray-200 rounded-lg hover:bg-gray-300 shadow">
                ← Kembali ke Daftar PO
            </a>

            @if ($purchaseOrder->status_po === 'Pending')
            <div class="flex space-x-3 mt-4 md:mt-0">
                <form id="rejectForm" action="{{ route('po.reject', $purchaseOrder->po_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="button" onclick="confirmReject()"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">
                        Tolak PO
                    </button>
                </form>

                <form id="approveForm" action="{{ route('po.approve', $purchaseOrder->po_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="button" onclick="confirmApprove()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                        Setujui PO
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- ================= SWEETALERT ================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmApprove() {
    Swal.fire({
        title: 'Setujui Purchase Order?',
        text: 'PO akan disetujui dan tidak dapat diubah kembali.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('approveForm').submit();
        }
    });
}

function confirmReject() {
    Swal.fire({
        title: 'Tolak Purchase Order?',
        text: 'PO yang ditolak tidak dapat diproses kembali.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('rejectForm').submit();
        }
    });
}
</script>

{{-- ================= FLASH MESSAGE ================= --}}
@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '{{ session('success') }}',
    timer: 2000,
    showConfirmButton: false
});
</script>
@endif

@if (session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '{{ session('error') }}'
});
</script>
@endif

@if (session('warning'))
<script>
Swal.fire({
    icon: 'warning',
    title: 'Perhatian',
    text: '{{ session('warning') }}'
});
</script>
@endif

@endsection
