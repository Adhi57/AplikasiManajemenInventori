@extends('layouts.app')
@section('page-title',  'Retur Barang')
@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-6">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow-md">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Daftar Retur Barang</h1>

            <form method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode barang / PO ID"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Diproses" {{ $status == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-600 text-sm">
                    <tr>
                        <th class="py-3 px-4 text-left">#</th>
                        <th class="py-3 px-4 text-left">PO ID</th>
                        <th class="py-3 px-4 text-left">Kode Barang</th>
                        <th class="py-3 px-4 text-center">Qty Retur (Karton)</th>
                        <th class="py-3 px-4 text-left">Alasan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Tanggal Retur</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @forelse ($returs as $index => $retur)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $returs->firstItem() + $index }}</td>
                        <td class="py-3 px-4">{{ $retur->po_id }}</td>
                        <td class="py-3 px-4">{{ $retur->kode_barang }}</td>
                        <td class="py-3 px-4 text-center">{{ $retur->qty_retur }}</td>
                        <td class="px-6 py-4 text-gray-800">
                            <div
                                x-data="{ editing: false, alasan: '{{ $retur->alasan }}' }"
                                @click.away="editing = false"
                                class="relative">
                                {{-- Teks normal --}}
                                <div x-show="!editing" class="cursor-pointer hover:bg-gray-100 p-1 rounded"
                                    @click="editing = true">
                                    <span x-text="alasan || 'Klik untuk isi alasan'"></span>
                                </div>

                                {{-- Input edit --}}
                                <div x-show="editing" class="flex gap-2">
                                    <input type="text" x-model="alasan"
                                        class="border border-gray-300 rounded px-2 py-1 w-full text-sm focus:ring focus:ring-blue-200"
                                        @keydown.enter.prevent="
                       fetch('{{ route('retur.updateAlasan', $retur->retur_id) }}', {
                           method: 'PATCH',
                           headers: { 
                               'Content-Type': 'application/json',
                               'X-CSRF-TOKEN': '{{ csrf_token() }}'
                           },
                           body: JSON.stringify({ alasan })
                       })
                       .then(res => res.json())
                       .then(data => { if(data.success){ editing = false } })
                   ">
                                    <button @click="editing = false" class="text-gray-500 hover:text-gray-700 text-sm">Batal</button>
                                </div>
                            </div>
                        </td>

                        <td class="py-3 px-4 text-center">
                                @if ($retur->status_retur === 'Pending')
                                    <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">Pending</span>
                                @elseif ($retur->status_retur === 'Disetujui')
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Disetujui</span>
                                @elseif ($retur->status_retur === 'Selesai')
                                    <span class="inline-block px-3 py-1 bg-red-700 text-white rounded-full text-sm font-medium">Ditolak</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">{{ $retur->tanggal_retur?->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('retur.show', $retur->retur_id) }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-xs">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">Tidak ada data retur barang</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $returs->links() }}
        </div>
    </div>
</div>
@endsection