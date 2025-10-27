@extends('layouts.app')

@section('page-title', 'Approval PO')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-gray-800">Daftar Surat Purchase Order</h1>
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    @if ($PO_List->count())
        @foreach ($PO_List as $PO_List)
            @php
                $statusColor = 'bg-gray-200 text-gray-800'; 
                if ($PO_List->status_po == 'Disetujui') {
                    $statusColor = 'bg-green-600 text-white'; 
                } elseif ($PO_List->status_po == 'Ditolak') {
                    $statusColor = 'bg-red-600 text-white'; 
                } elseif ($PO_List->status_po == 'Pending') {
                    $statusColor = 'bg-yellow-400 text-yellow-900'; 
                }
            @endphp
            
            <div class="flex w-full my-3 p-5 bg-white rounded-md shadow-lg justify-between items-center">
                
                <div class="flex items-center space-x-5">
                    <i class="fa-solid fa-file-invoice bg-red-700 p-4 text-2xl text-white rounded-md"></i>
                    <div class="flex flex-col">
                        <p class="text-md font-semibold">{{ $PO_List->po_id }}</p>
                        <p class="text-sm font-normal text-gray-500">{{ $PO_List->user->nama_lengkap }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 ml-10">
                    
                     <div class="ml-10 {{ $statusColor }} px-9 items-center rounded-2xl">
                        <p class="text-sm text-center font-medium rounded-xl py-1 ">{{ $PO_List->status_po }}</p>
                    </div>

                    <a href={{ route('approval.show_po', $PO_List->po_id) }} class="text-sm text-white font-medium rounded-xl bg-red-700 px-6 py-2 transition duration-150 hover:bg-red-800">
                        Lihat Surat &rarr;
                    </a>

                    <button class="bg-green-500 p-3 text-white rounded-md transition duration-150 hover:bg-green-600">
                        <i class="fa-solid fa-square-check text-xl"></i>
                    </button>
                    
                    <button class="bg-red-600 p-3 text-white rounded-md transition duration-150 hover:bg-red-700">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                    
                    <button class="bg-gray-500 p-3 text-white rounded-md transition duration-150 hover:bg-gray-600">
                        <i class="fa-solid fa-file-pdf text-xl"></i>
                    </button>
                </div>

            </div>
        @endforeach
    @else
        <div class="text-center p-10 bg-white rounded-lg shadow-lg">
            <p class="text-xl text-gray-500">Surat PO tidak ditemukan. Seilahkan Tambahkan Pada Permintaan Pembelian!</p>
        </div>
    @endif
</div>


@endsection
