@extends('layouts.app')
@section('page-title', 'Detail PO - ' . $purchaseOrder->po_id)
@section('content')

    <x-page-header title="Detail Purchase Order" description="Review dokumen PO sebelum memberikan persetujuan" icon="fa-file-lines">
    <x-slot name="actions">
        <a href="{{ route('approval.approval_po') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </x-slot>
</x-page-header>

    {{-- Status Header --}}
    @php
        $statusCfg = match ($purchaseOrder->status_po) {
            'Pending' => ['bg' => 'from-amber-500 to-amber-600', 'icon' => 'fa-clock', 'label' => 'Menunggu Persetujuan'],
            'Disetujui' => ['bg' => 'from-emerald-500 to-emerald-600', 'icon' => 'fa-circle-check', 'label' => 'Disetujui'],
            'Ditolak' => ['bg' => 'from-red-500 to-red-600', 'icon' => 'fa-circle-xmark', 'label' => 'Ditolak'],
            'Diterima' => ['bg' => 'from-blue-500 to-blue-600', 'icon' => 'fa-box-open', 'label' => 'Diterima di Gudang'],
            default => ['bg' => 'from-gray-500 to-gray-600', 'icon' => 'fa-question', 'label' => $purchaseOrder->status_po],
        };
    @endphp
    <div
        class="bg-gradient-to-r {{ $statusCfg['bg'] }} rounded-2xl p-5 mb-6 shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fa-solid {{ $statusCfg['icon'] }} text-white text-xl"></i>
            </div>
            <div>
                <p class="text-white/70 text-xs uppercase font-bold tracking-wide">Status Purchase Order</p>
                <p class="text-white text-lg font-bold">{{ $statusCfg['label'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-red-700 hover:bg-red-800 transition duration-300 text-white px-4 py-2 rounded-xl text-sm font-bold">
                <a href="{{ route('approval.print_po', $purchaseOrder->po_id) }}">
                    <i class="fa-solid fa-print text-xs mr-1"></i>Print PO
                </a>
            </span>
            <span class="bg-white/20 text-white px-4 py-2 rounded-xl text-sm font-bold">
                <i class="fa-solid fa-hashtag text-xs mr-1"></i>{{ $purchaseOrder->po_id }}
            </span>
            <span class="bg-white/20 text-white px-4 py-2 rounded-xl text-sm font-bold">
                <i class="fa-regular fa-calendar text-xs mr-1"></i>
                {{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d M Y') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Info Perusahaan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-building text-red-500"></i>
                    Info Perusahaan
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <p class="font-bold text-gray-800">{{ $appSettings['nama_perusahaan'] ?? 'CV. Berkah Jaya Lumintu' }}</p>
                <p class="text-sm text-gray-600">{{ $appSettings['alamat_perusahaan'] ?? '-' }}</p>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-phone text-gray-400 text-xs"></i> {{ $appSettings['telepon_perusahaan'] ?? '-' }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-envelope text-gray-400 text-xs"></i> {{ $appSettings['email_perusahaan'] ?? '-' }}
                </div>
            </div>
        </div>

        {{-- Info Supplier --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-truck-field text-red-500"></i>
                    Supplier
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <p class="font-bold text-gray-800">{{ $purchaseOrder->supplier->namaSupplier ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $purchaseOrder->supplier->alamatSupplier ?? 'Alamat tidak tersedia' }}
                </p>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-phone text-gray-400 text-xs"></i> {{ $purchaseOrder->supplier->noTelepon ?? '-' }}
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fa-solid fa-envelope text-gray-400 text-xs"></i> {{ $purchaseOrder->supplier->email ?? '-' }}
                </div>
            </div>
        </div>

        {{-- Info PO --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-red-500"></i>
                    Detail PO
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Dibuat Oleh</p>
                    <p class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-red-500 text-[9px]"></i>
                        </span>
                        {{ $purchaseOrder->user->nama_lengkap ?? '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Jumlah Item</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $purchaseOrder->details->count() }} Produk</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Syarat Pembayaran</p>
                    <p class="text-sm text-gray-600">Maks. 30 hari setelah PO</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-red-500"></i>
                Daftar Barang
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider border-b">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Produk</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-center">Satuan</th>
                        <th class="px-4 py-3 text-right">Harga Satuan</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $subtotal = 0; @endphp
                    @foreach ($purchaseOrder->details as $idx => $detail)
                        @php
                            $itemTotal = $detail->quantity * $detail->harga_satuan;
                            $subtotal += $itemTotal;
                        @endphp
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-mono">{{ $detail->kode_barang }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold">{{ number_format($detail->quantity, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $detail->satuan }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">Rp
                                {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp
                                {{ number_format($itemTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>

                @php
                    $ppnRate = floatval($appSettings['ppn_persen'] ?? 11) / 100;
                    $pajak = $subtotal * $ppnRate;
                    $total = $subtotal + $pajak;
                @endphp
                <tfoot class="bg-gray-50">
                    <tr class="border-t border-gray-200">
                        <td colspan="6" class="px-4 py-2.5 text-right font-semibold text-gray-600">Subtotal</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp
                            {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="px-4 py-2.5 text-right font-semibold text-gray-600">Pajak ({{ $appSettings['ppn_persen'] ?? 11 }}%)</td>
                        <td class="px-4 py-2.5 text-right text-red-600 font-semibold">Rp
                            {{ number_format($pajak, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t-2 border-gray-300">
                        <td colspan="6" class="px-4 py-3 text-right font-bold text-gray-800 text-base">Jumlah Total</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-700 text-base">Rp
                            {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-500">
                <i class="fa-solid fa-info-circle text-gray-400 mr-1"></i>
                Pembayaran dilakukan maksimal 30 hari setelah tanggal PO.
            </div>

            @if(auth()->user()->role === 'Head' || auth()->user()->role === 'SuperAdmin')
                @if ($purchaseOrder->status_po === 'Pending')
                    <div class="flex items-center gap-3">
                        <form id="rejectForm" action="{{ route('po.reject', $purchaseOrder->po_id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="button" onclick="confirmReject()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow">
                                <i class="fa-solid fa-circle-xmark"></i> Tolak PO
                            </button>
                        </form>
                        <form id="approveForm" action="{{ route('po.approve', $purchaseOrder->po_id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="button" onclick="confirmApprove()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white font-semibold text-sm rounded-xl hover:bg-emerald-700 transition shadow">
                                <i class="fa-solid fa-circle-check"></i> Setujui PO
                            </button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmApprove() {
                Swal.fire({
                    title: 'Setujui Purchase Order?',
                    text: 'PO akan disetujui dan tidak dapat diubah kembali.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Ya, Setujui',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById('approveForm').submit();
                });
            }

            function confirmReject() {
                Swal.fire({
                    title: 'Tolak Purchase Order?',
                    text: 'PO yang ditolak tidak dapat diproses kembali.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: '<i class="fa-solid fa-xmark mr-1"></i> Ya, Tolak',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) document.getElementById('rejectForm').submit();
                });
            }
        </script>
    @endpush

@endsection