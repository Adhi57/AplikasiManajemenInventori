@extends('layouts.app')
@section('page-title',  'Surat Jalan ')
@section('content')

<style>
    @media print {
        #main-topbar, 
        #main-navbar, 
        .hidden-on-print { 
            display: none !important;
        }

        body, #app {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .max-w-4xl.mx-auto {
            max-width: none !important;
            margin: 0 !important;
        }
    }
</style>

<div class="max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg print:p-0 print:shadow-none">

    {{-- HEADER --}}
    <header class="text-center mb-6">
        <h2 class="text-xl font-bold mb-1 print:text-lg">SURAT JALAN BARANG</h2>
        <p class="text-sm font-semibold">CV. Berkah Jaya Lumintu</p>
        <p class="text-xs">Jl. Wonokerso I, Wonokerso, Kecamatan Tembarak, Kabupaten Temanggung</p>
    </header>

    <hr class="mb-4 border-gray-400">

    {{-- DETAIL SURAT --}}
    <div class="text-sm mb-6">
        <div class="grid grid-cols-2 gap-x-12">
            <div>
                <p><strong>Nomor:</strong> {{ $suratJalan->sj_id ?? '...........' }}</p>
                <p><strong>Tanggal:</strong> 
                    {{ $suratJalan->tanggal_surat 
                        ? \Carbon\Carbon::parse($suratJalan->tanggal_surat)->translatedFormat('d F Y')
                        : '...........' }}
                </p>
                <p><strong>Status:</strong> {{ $suratJalan->status ?? '...........' }}</p>
            </div>
            <div>
                <p class="mb-1"><strong>Tujuan:</strong></p>
                <p class="pl-4 leading-tight">
                    {{ $suratJalan->pelanggan->nama_pelanggan ?? '..............................' }}<br>
                    {{ $suratJalan->alamat_penerima ?? '..............................' }}<br>
                    ({{ $suratJalan->pelanggan->PIC ?? '...................' }})
                </p>
            </div>
        </div>
    </div>

    {{-- TABEL BARANG --}}
    <div class="overflow-x-auto mb-10 border border-gray-300 rounded-lg">
        <table class="min-w-full text-sm text-gray-700 border-collapse">
            <thead class="bg-gray-50 border-b border-gray-300">
                <tr class="text-left font-semibold text-gray-800">
                    <th class="py-2 px-3 border-r w-1/12">No.</th>
                    <th class="py-2 px-3 border-r w-4/12">Nama Barang</th>
                    <th class="py-2 px-3 border-r w-2/12">Kode Barang</th>
                    <th class="py-2 px-3 border-r w-1/12 text-center">Jumlah</th>
                    <th class="py-2 px-3 border-r w-1/12 text-center">Satuan</th>
                    <th class="py-2 px-3 w-3/12 text-right">Harga Total</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @forelse($suratJalan->details as $index => $detail)
                    @php
                        $hargaJual = $detail->barang->harga_jual ?? 0;
                        $totalItem = $detail->quantity * $hargaJual;
                        $subtotal += $totalItem;
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3 border-r">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 border-r">{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td class="py-2 px-3 border-r">{{ $detail->kode_barang }}</td>
                        <td class="py-2 px-3 border-r text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 border-r text-center">{{ $detail->satuan ?? '-' }}</td>
                        <td class="py-2 px-3 text-right">{{ 'Rp ' . number_format($totalItem, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-400">
                            Tidak ada barang dalam surat jalan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- RINGKASAN --}}
            @php
                $pajak = $subtotal * 0.11;
                $diskonPersen = floatval($suratJalan->diskon_pelanggan/100 ?? 0);
                $diskon = $subtotal * $diskonPersen;
                $biaya_kirim = floatval($suratJalan->biaya_pengiriman ?? 0);
                $grandTotal = $subtotal + $pajak + $biaya_kirim - $diskon;
            @endphp


            <tfoot class="bg-gray-50 text-gray-800">
                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Subtotal</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300">{{ 'Rp ' . number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Pajak (11%)</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300">{{ 'Rp ' . number_format($pajak, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Biaya Pengiriman</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300">{{ 'Rp ' . number_format($biaya_kirim, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Diskon Pelanggan</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300"> - {{ 'Rp ' . number_format($diskon, 2, ',', '.') }}</td>
                </tr>

                <tr>
                    <td colspan="5" class="text-right font-bold py-2 px-3 border-t border-gray-400 text-lg">Total Akhir</td>
                    <td class="text-right font-bold py-2 px-3 border-t border-gray-400 text-lg text-green-700">
                        {{ 'Rp ' . number_format($grandTotal, 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="grid grid-cols-2 gap-4 text-center text-sm mt-12">
        <div>
            <p>Dikeluarkan oleh,</p>
            <br><br><br>
            <p>( Ttd )</p>
            <p class="mt-4 font-semibold">{{ $suratJalan->user->nama_lengkap ?? '....................................' }}</p>
            <p class="text-xs text-gray-600">CV. Berkah Jaya Lumintu</p>
        </div>
        <div>
            <p>Penerima,</p>
            <br><br><br>
            <p>( Ttd )</p>
            <p class="mt-4 font-semibold">{{ $suratJalan->nama_penerima ?? '....................................' }}</p>
        </div>
    </div>
    
    <div class="flex flex-col md:flex-row justify-between items-center mt-6">
            {{-- Tombol Kembali --}}
            <a href="{{ route('approval.approval_surat_jalan') }}" class="w-full md:w-auto px-4 py-2 mb-4 md:mb-0 text-center text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150 shadow-md"> <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar PO </a>
            {{-- Tombol Aksi --}}
            <div class="flex justify-end space-x-3"> 
                @if ($suratJalan->status === 'Pending')
                {{-- Tombol Tolak --}}
                <form action="{{ route('sj.reject', $suratJalan->sj_id) }}" method="POST" id="rejectForm"> @csrf @method('PUT') <button type="button" onclick="confirmReject()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow"> Tolak Surat Jalan </button> </form>
                {{-- Tombol Setujui --}}
                <form action="{{ route('sj.approve', $suratJalan->sj_id) }}" method="POST" id="approveForm"> 
                    @csrf 
                    @method('PUT') 
                    <button type="button" onclick="confirmApprove()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow"> Setujui Surat Jalan </button> 
                </form> 
                @endif
            </div>
        </div>
    </div>
</div>
{{-- SweetAlert --}}
<script>
function confirmApprove() {
    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin menyetujui Surat Jalan ini?",
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
        title: 'Tolak Surat Jalan?',
        text: "Apakah Anda yakin ingin menolak Surat Jalan ini?",
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
}

@if(session('success'))
    Swal.fire('Berhasil!', '{{ session('success') }}', 'success');
@elseif(session('error'))
    Swal.fire('Gagal!', '{{ session('error') }}', 'error');
@elseif(session('warning'))
    Swal.fire('Perhatian', '{{ session('warning') }}', 'warning');
@endif
</script>

@endsection
