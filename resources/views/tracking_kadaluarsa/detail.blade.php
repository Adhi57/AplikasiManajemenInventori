@extends('layouts.app')

@section('page-title', 'Detail Tracking Kadaluarsa')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        @php
            $barang = $stok->first()->barang;
        @endphp

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('tracking_kadaluarsa.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <span class="text-xs text-gray-400 font-mono">{{ $stok->first()->kode_barang }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $barang->nama_barang }}</h1>
                <p class="text-sm text-gray-500 mt-1">Detail batch kadaluarsa untuk produk ini.</p>
            </div>
            <a href="{{ route('tracking_kadaluarsa.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        {{-- CHART --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-amber-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Grafik Sisa Hari Kadaluarsa</h2>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <canvas id="expiryChart" height="110"></canvas>
            </div>
        </div>

        {{-- BATCH TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-red-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Daftar Batch Kadaluarsa</h2>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">{{ $stok->count() }}
                    batch</span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kadaluarsa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Sisa Hari</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok (Karton)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Total (PCS)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($stok as $s)
                            @php
                                $expiryDate = strtotime($s->tgl_kadaluarsa);
                                $daysLeft = floor(($expiryDate - time()) / (60 * 60 * 24));
                                $isi = $s->barang->jml_barang_per_karton ?? 1;

                                $isExpired = $daysLeft < 0;
                                $isCritical = $daysLeft >= 0 && $daysLeft < 30;
                                $isWarning = $daysLeft >= 30 && $daysLeft < 60;
                                $rowBg = $isExpired ? 'bg-gray-50' : ($isCritical ? 'bg-red-50' : ($isWarning ? 'bg-amber-50' : ''));
                            @endphp

                            <tr class="hover:bg-blue-50/30 transition-colors {{ $rowBg }}">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ date('d M Y', $expiryDate) }}</td>

                                <td class="px-4 py-3 text-center">
                                    @if($isExpired)
                                        <span
                                            class="px-2.5 py-1 bg-gray-200 text-gray-600 text-xs font-bold rounded-full">Expired</span>
                                    @elseif($isCritical)
                                        <span
                                            class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $daysLeft }}
                                            hari</span>
                                    @elseif($isWarning)
                                        <span
                                            class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">{{ $daysLeft }}
                                            hari</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">{{ $daysLeft }}
                                            hari</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-lg">
                                        {{ number_format($s->jumlah_stok, 2, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center font-semibold text-gray-800">
                                    {{ number_format($s->jumlah_stok * $isi, 0, ',', '.') }} pcs
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('tracking_kadaluarsa.destroy', $s->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus batch stok ini? Data tidak dapat dipulihkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors shadow-sm">
                                            <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const expiryLabels = [
            @foreach($stok as $s)
                "{{ date('d M Y', strtotime($s->tgl_kadaluarsa)) }}",
            @endforeach
        ];

        const expiryDays = [
            @foreach($stok as $s)
                {{ floor((strtotime($s->tgl_kadaluarsa) - time()) / 86400) }},
            @endforeach
        ];

        const barColors = expiryDays.map(day => {
            if (day < 0) return "rgba(156, 163, 175, 0.7)";
            if (day < 30) return "rgba(239, 68, 68, 0.85)";
            if (day < 60) return "rgba(245, 158, 11, 0.85)";
            return "rgba(16, 185, 129, 0.85)";
        });

        new Chart(document.getElementById('expiryChart'), {
            type: 'bar',
            data: {
                labels: expiryLabels,
                datasets: [{
                    label: 'Sisa Hari',
                    data: expiryDays,
                    backgroundColor: barColors,
                    borderRadius: 6,
                    maxBarThickness: 40,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        cornerRadius: 8,
                        padding: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', drawBorder: false },
                        ticks: { font: { size: 11 }, color: '#94a3b8' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '500' }, color: '#374151' }
                    }
                }
            }
        });
    </script>

@endsection