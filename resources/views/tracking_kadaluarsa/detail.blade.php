@extends('layouts.app')

@section('page-title', 'Detail Tracking Kadaluarsa')

@section('content')

<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-xl p-8 border border-gray-200">

        {{-- Header Barang --}}
        @php
            $barang = $stok->first()->barang;
        @endphp

        <div class="mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">{{ $barang->nama_barang }}</h1>
            <p class="text-sm text-gray-500">Kode Barang: {{ $stok->first()->kode_barang }}</p>
        </div>

        {{-- Grafik --}}
        <div class="mb-10">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Grafik Sisa Hari Kadaluarsa</h2>
            <div class="bg-gray-100 p-4 rounded-lg border shadow-inner">
                <canvas id="expiryChart" height="110"></canvas>
            </div>
        </div>

        {{-- Table Batch --}}
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Daftar Batch Kadaluarsa</h2>

        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
            <table class="w-full text-sm text-gray-700">
                <thead>
                    <tr class="bg-red-600 text-white uppercase text-xs font-semibold">
                        <th class="p-4 text-left">Tanggal Kadaluarsa</th>
                        <th class="p-4 text-center">Sisa Hari</th>
                        <th class="p-4 text-center">Stok (Karton)</th>
                        <th class="p-4 text-center">Total (PCS)</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($stok as $s)

                        @php
                            $expiryDate = strtotime($s->tgl_kadaluarsa);
                            $daysLeft = floor(($expiryDate - time()) / (60*60*24));
                            $isi = $s->barang->jml_barang_per_karton ?? 1;

                            $rowClass = $daysLeft < 30 ? 'bg-red-100 text-red-700 font-semibold'
                                        : ($daysLeft < 60 ? 'bg-yellow-100 text-yellow-700 font-medium'
                                        : '');
                        @endphp

                        <tr class="border-b border-gray-100 hover:bg-red-50 transition {{ $rowClass }}">
                            <td class="p-4">{{ date('d M Y', $expiryDate) }}</td>

                            <td class="p-4 text-center">{{ $daysLeft }} hari</td>

                            <td class="p-4 text-center font-bold text-red-700">
                                {{ number_format($s->jumlah_stok, 2, ',', '.') }}
                            </td>

                            <td class="p-4 text-center">
                                {{ number_format($s->jumlah_stok * $isi, 0, ',', '.') }} pcs
                            </td>

                            <td class="p-4 text-center">

                                {{-- Hapus --}}
                                <form action="{{ route('tracking_kadaluarsa.destroy', $s->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus batch stok ini? Data tidak dapat dipulihkan.')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg shadow hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tombol Kembali --}}
        <div class="mt-6">
            <a href="{{ route('tracking_kadaluarsa.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg shadow hover:bg-gray-700">
                Kembali
            </a>
        </div>

    </div>
</div>


{{-- =============== CHART.JS SCRIPT =============== --}}
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
        if (day < 30) return "rgba(220, 38, 38, 0.9)";      // Merah
        if (day < 60) return "rgba(234, 179, 8, 0.9)";      // Kuning
        return "rgba(22, 163, 74, 0.9)";                    // Hijau
    });

    const ctx = document.getElementById('expiryChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: expiryLabels,
            datasets: [{
                label: 'Sisa Hari',
                data: expiryDays,
                backgroundColor: barColors,
                borderRadius: 6,
                barThickness: 40,
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: "#374151" },
                    grid: { color: "#e5e7eb" }
                },
                x: {
                    ticks: { color: "#374151" },
                    grid: { display: false }
                }
            }
        }
    });
</script>

@endsection
