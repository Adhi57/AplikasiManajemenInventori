@extends('layouts.app')

@section('page-title', 'Approval Surat Jalan')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-gray-800">Daftar Surat Purchase Order</h1>

<div class="container mx-auto p-4 sm:p-6 lg:p-8">

    {{-- 🔍 Filter Status --}}
    <form method="GET" action="{{ route('approval.approval_po') }}" class="flex flex-wrap items-center gap-4 mb-6">
        <label for="status" class="font-medium text-gray-700">Filter Status:</label>
        <select name="status" id="status" class="border rounded-lg p-2 px-8 text-sm">
            <option value="">Semua</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
            Terapkan
        </button>
    </form>

    @if ($suratJalans->count())
    @foreach ($suratJalans as $sj)
    @php
    $statusColor = match($sj->status) {
    'Disetujui' => 'bg-green-600 text-white',
    'Ditolak' => 'bg-red-600 text-white',
    'Pending' => 'bg-yellow-400 text-yellow-900',
    default => 'bg-gray-200 text-gray-800',
    };
    @endphp

    <div class="flex max-w-6xl m-auto my-3 p-5 bg-white rounded-md shadow-lg justify-between items-center">

        <div class="flex items-center space-x-5">
            <i class="fa-solid fa-file-invoice bg-red-700 p-4 text-2xl text-white rounded-md"></i>
            <div class="flex flex-col">
                <p class="text-md font-semibold">{{ $sj->sj_id }}</p>
                <p class="text-sm text-gray-500">Nama Pelanggan: {{ $sj->pelanggan->nama_pelanggan ?? '-' }}</p>
                <p class="text-sm text-gray-500">Tanggal Surat: {{ \Carbon\Carbon::parse ($sj->tanggal_surat ?? '-') ->format('d M Y')}}</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 ml-10">
            <div class="{{ $statusColor }} px-8 rounded-2xl text-center">
                <p class="text-sm font-medium py-1">{{ $sj->status }}</p>
            </div>

            {{-- TOMBOL LIHAT --}}
            <a href="{{ route('approval.show_surat_jalan', $sj->sj_id) }}"
                class="text-sm text-white font-medium rounded-xl bg-red-700 px-6 py-2 transition duration-150 hover:bg-red-800">
                Lihat Surat &rarr;
            </a>
        </div>
    </div>
    @endforeach
    @else
    <div class="text-center p-10 bg-white rounded-lg shadow-lg">
        <p class="text-xl text-gray-500">Surat PO tidak ditemukan. Silakan tambahkan dari permintaan pembelian!</p>
    </div>
    @endif
</div>
@endsection