@extends('layouts.app')

@section('page-title', 'Approval PO')

@section('content')

<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">

    {{-- HEADER --}}
    <div class="max-w-7xl mx-auto mb-6 p-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800">Approval Purchase Order</h1>
        <p class="text-sm text-gray-600 mt-1">
            Lihat dan proses permintaan pembelian yang diajukan oleh tim procurement.
        </p>
    </div>

    {{-- FILTER --}}
    <div class="max-w-7xl mx-auto mb-8 p-5 bg-white shadow-sm rounded-xl border border-gray-200">
        <form method="GET" action="{{ route('approval.approval_po') }}" class="flex flex-wrap items-center gap-4">

            {{-- Search --}}
            <div class="relative flex-grow min-w-[230px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Cari PO ID atau Pembuat..."
                    class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm focus:border-red-600 focus:ring-red-600">
            </div>

            {{-- Status --}}
            <select name="status" id="status"
                class="border border-gray-300 rounded-lg p-2 text-sm shadow-sm focus:border-red-600 focus:ring-red-600">
                <option value="">Semua Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima (Gudang)</option>
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

    {{-- LIST PO --}}
    <div class="max-w-7xl mx-auto">

        @if ($PO_List->count())
            <div class="space-y-4">

                @foreach ($PO_List as $PO)
                    @php
                        $statusColor = match($PO->status_po) {
                            'Disetujui' => 'bg-green-100 text-green-700 border-green-300',
                            'Ditolak' => 'bg-red-100 text-red-700 border-red-300',
                            'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                            'Diterima' => 'bg-blue-100 text-blue-700 border-blue-300',
                            default => 'bg-gray-100 text-gray-700 border-gray-300',
                        };
                    @endphp

                    {{-- CARD PO --}}
                    <div class="p-5 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center hover:border-red-400 transition">

                        {{-- LEFT --}}
                        <div class="flex items-start space-x-4 w-full md:w-3/5 mb-4 md:mb-0">

                            {{-- Icon --}}
                            <div class="flex-shrink-0 bg-red-600 p-3 rounded-xl">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>

                            {{-- Detail --}}
                            <div class="flex flex-col">
                                <p class="text-lg font-semibold text-red-700">{{ $PO->po_id }}</p>
                                <p class="text-sm text-gray-700">
                                    Dibuat oleh: <span class="font-medium">{{ $PO->user->nama_lengkap ?? '-' }}</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Tanggal: {{ \Carbon\Carbon::parse($PO->tanggal_po)->format('d F Y') }}
                                </p>

                            </div>
                        </div>

                        {{-- RIGHT --}}
                        <div class="flex flex-col sm:flex-row items-center sm:space-x-4 w-full md:w-2/5 justify-end">

                            <div class="{{ $statusColor }} px-4 py-1.5 rounded-full text-center border font-semibold text-xs w-32">
                                {{ $PO->status_po }}
                            </div>

                            <a href="{{ route('approval.show_po', $PO->po_id) }}"
                                class="mt-3 sm:mt-0 text-sm text-white font-medium rounded-lg bg-red-600 px-6 py-2.5 hover:bg-red-700 shadow-sm text-center">
                                Detail &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach

            </div>
        @else
            {{-- Not Found --}}
            <div class="text-center p-10 bg-white rounded-xl shadow-sm border border-gray-200">
                <p class="text-lg text-gray-500 font-medium">
                    Tidak ada data Purchase Order yang sesuai filter.
                </p>
            </div>
        @endif

    </div>
</div>
@endsection
