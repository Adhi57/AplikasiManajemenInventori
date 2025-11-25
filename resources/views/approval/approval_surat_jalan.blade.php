@extends('layouts.app')

@section('page-title', 'Approval Surat Jalan')

@section('content')

<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">

    {{-- HEADER --}}
    <div class="max-w-7xl mx-auto mb-6 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800">Approval Surat Jalan</h1>
        <p class="text-sm text-gray-600 mt-1">
            Lihat dan proses persetujuan surat jalan.
        </p>
    </div>

    {{-- FILTER --}}
    <div class="max-w-7xl mx-auto mb-8 p-5 bg-white shadow-sm rounded-xl border border-gray-200">

        <form method="GET" action="{{ route('approval.approval_po') }}"
            class="flex flex-wrap items-center gap-4">

            <label for="status" class="text-sm font-medium text-gray-700">Filter Status:</label>
            <select name="status" id="status"
                class="border border-gray-300 rounded-lg p-2 text-sm shadow-sm focus:border-red-600 focus:ring-red-600">
                <option value="">Semua</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
            </select>

            <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm shadow-sm hover:bg-red-700">
                Terapkan
            </button>

            <a href="{{ route('approval.approval_po') }}"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm shadow-sm hover:bg-gray-500">
                Reset
            </a>
        </form>
    </div>

    {{-- LIST SURAT JALAN --}}
    <div class="max-w-7xl mx-auto">

        @if ($suratJalans->count())

            <div class="space-y-4">

                @foreach ($suratJalans as $sj)
                    @php
                        $statusColor = match ($sj->status) {
                            'Disetujui' => 'bg-green-100 text-green-700 border-green-300',
                            'Ditolak' => 'bg-red-100 text-red-700 border-red-300',
                            'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                            'Diterima' => 'bg-blue-100 text-blue-700 border-blue-300',
                            default => 'bg-gray-100 text-gray-700 border-gray-300',
                        };
                    @endphp

                    {{-- CARD --}}
                    <div
                        class="p-5 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between
                        items-start md:items-center hover:border-red-400 transition">

                        {{-- LEFT SECTION --}}
                        <div class="flex items-start space-x-4 w-full md:w-3/5 mb-4 md:mb-0">

                            {{-- Icon --}}
                            <div class="flex-shrink-0 bg-red-600 p-3 rounded-xl">
                                <i class="fa-solid fa-file-invoice text-white text-xl"></i>
                            </div>

                            <div class="flex flex-col">
                                <p class="text-lg font-semibold text-red-700">{{ $sj->sj_id }}</p>
                                <p class="text-sm text-gray-700">
                                    Pelanggan:
                                    <span class="font-medium">{{ $sj->pelanggan->nama_pelanggan ?? '-' }}</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Tanggal: {{ \Carbon\Carbon::parse($sj->tanggal_surat)->format('d F Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- RIGHT SECTION --}}
                        <div class="flex flex-col sm:flex-row items-center sm:space-x-4 w-full md:w-2/5 justify-end">

                            {{-- BADGE --}}
                            <div class="{{ $statusColor }} px-4 py-1.5 rounded-full text-center border font-semibold text-xs w-32">
                                {{ $sj->status }}
                            </div>

                            {{-- BUTTON --}}
                            <a href="{{ route('approval.show_surat_jalan', $sj->sj_id) }}"
                                class="mt-3 sm:mt-0 text-sm text-white font-medium rounded-lg bg-red-600 px-6 py-2.5 hover:bg-red-700 shadow-sm">
                                Lihat Surat &rarr;
                            </a>
                        </div>
                    </div>

                @endforeach

            </div>

        @else
            {{-- NO DATA --}}
            <div class="text-center p-10 bg-white rounded-xl shadow-sm border border-gray-200">
                <p class="text-lg text-gray-500 font-medium">
                    Tidak ada surat jalan ditemukan.
                </p>
            </div>
        @endif

    </div>
</div>

@endsection
