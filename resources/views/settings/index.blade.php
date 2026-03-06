@extends('layouts.app')
@section('page-title', 'Pengaturan')

@section('content')
    <div class="p-4 sm:p-6 bg-gray-50 rounded-xl min-h-[80vh]">
        <div class="max-w-5xl mx-auto" x-data="{ activeTab: 'profil' }">

            {{-- SUCCESS / ERROR ALERTS --}}
            @if(session('success'))
                <div
                    class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                    <div class="flex items-center gap-2 font-medium mb-1">
                        <i class="fa-solid fa-triangle-exclamation text-red-500"></i> Terdapat kesalahan:
                    </div>
                    <ul class="list-disc list-inside ml-5 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- PAGE HEADER --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="relative">
                    <div class="h-28 bg-gradient-to-br from-red-700 via-red-800 to-red-950 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 800 200" preserveAspectRatio="none">
                                <path d="M0,100 C150,200 350,0 500,100 C650,200 750,50 800,100 L800,200 L0,200 Z"
                                    fill="white" />
                            </svg>
                        </div>
                    </div>
                    <div class="px-6 sm:px-8 pb-5 -mt-8 relative z-10">
                        <div class="flex items-end gap-4">
                            <div
                                class="w-16 h-16 bg-white rounded-2xl shadow-lg border-4 border-white flex items-center justify-center text-red-700 text-2xl shrink-0">
                                <i class="fa-solid fa-gear"></i>
                            </div>
                            <div class="pb-1">
                                <h1 class="text-xl font-bold text-gray-900">Pengaturan</h1>
                                <p class="text-sm text-gray-500">Kelola profil perusahaan dan pengaturan umum aplikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB NAVIGATION --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 mb-6">
                <div class="flex gap-1">
                    <button @click="activeTab = 'profil'" :class="activeTab === 'profil'
                                ? 'bg-gradient-to-r from-red-700 to-red-800 text-white shadow-lg shadow-red-200'
                                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold text-sm transition-all duration-200">
                        <i class="fa-solid fa-building"></i>
                        <span>Profil Perusahaan</span>
                    </button>
                    <button @click="activeTab = 'umum'" :class="activeTab === 'umum'
                                ? 'bg-gradient-to-r from-red-700 to-red-800 text-white shadow-lg shadow-red-200'
                                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                        class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-semibold text-sm transition-all duration-200">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Pengaturan Umum</span>
                    </button>
                </div>
            </div>

            {{-- TAB CONTENT: PROFIL PERUSAHAAN --}}
            <div x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                <form action="{{ route('pengaturan.profil') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                        {{-- LEFT: Form Fields --}}
                        <div class="lg:col-span-7">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-building text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-semibold text-gray-800">Informasi Perusahaan</h2>
                                        <p class="text-[11px] text-gray-400">Data ini akan ditampilkan di dokumen cetak</p>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    {{-- Nama Perusahaan --}}
                                    <div>
                                        <label for="nama_perusahaan"
                                            class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                            Nama Perusahaan
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                <i class="fa-solid fa-building text-gray-300 text-sm"></i>
                                            </div>
                                            <input type="text" id="nama_perusahaan" name="nama_perusahaan"
                                                value="{{ old('nama_perusahaan', $settings['nama_perusahaan'] ?? '') }}"
                                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200"
                                                placeholder="PT. Nama Perusahaan">
                                        </div>
                                        @error('nama_perusahaan')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Alamat --}}
                                    <div>
                                        <label for="alamat_perusahaan"
                                            class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                            Alamat
                                        </label>
                                        <div class="relative">
                                            <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                                                <i class="fa-solid fa-location-dot text-gray-300 text-sm"></i>
                                            </div>
                                            <textarea id="alamat_perusahaan" name="alamat_perusahaan" rows="3"
                                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200 resize-none"
                                                placeholder="Jl. Contoh Alamat No. 123, Kota">{{ old('alamat_perusahaan', $settings['alamat_perusahaan'] ?? '') }}</textarea>
                                        </div>
                                        @error('alamat_perusahaan')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Telepon --}}
                                    <div>
                                        <label for="telepon_perusahaan"
                                            class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                            No. Telepon
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                <i class="fa-solid fa-phone text-gray-300 text-sm"></i>
                                            </div>
                                            <input type="text" id="telepon_perusahaan" name="telepon_perusahaan"
                                                value="{{ old('telepon_perusahaan', $settings['telepon_perusahaan'] ?? '') }}"
                                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200"
                                                placeholder="021-1234567">
                                        </div>
                                        @error('telepon_perusahaan')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div>
                                        <label for="email_perusahaan"
                                            class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                            Email Perusahaan
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                <i class="fa-solid fa-envelope text-gray-300 text-sm"></i>
                                            </div>
                                            <input type="email" id="email_perusahaan" name="email_perusahaan"
                                                value="{{ old('email_perusahaan', $settings['email_perusahaan'] ?? '') }}"
                                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200"
                                                placeholder="info@perusahaan.com">
                                        </div>
                                        @error('email_perusahaan')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: Logo Upload --}}
                        <div class="lg:col-span-5">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-image text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-semibold text-gray-800">Logo Perusahaan</h2>
                                        <p class="text-[11px] text-gray-400">Upload logo untuk kop surat</p>
                                    </div>
                                </div>

                                {{-- Current Logo Preview --}}
                                <div class="mb-5">
                                    <div
                                        class="w-full aspect-[3/2] bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden">
                                        @if(!empty($settings['logo_perusahaan']))
                                            <img src="{{ asset('storage/' . $settings['logo_perusahaan']) }}"
                                                alt="Logo Perusahaan" class="max-w-full max-h-full object-contain p-4">
                                        @else
                                            <div class="text-center">
                                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                                                <p class="text-xs text-gray-400">Belum ada logo</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Upload Input --}}
                                <div>
                                    <label for="logo_perusahaan"
                                        class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                        Upload Logo Baru
                                    </label>
                                    <input type="file" id="logo_perusahaan" name="logo_perusahaan"
                                        accept="image/jpg,image/jpeg,image/png,image/webp"
                                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 file:cursor-pointer file:transition-all cursor-pointer">
                                    <p class="text-[11px] text-gray-400 mt-1.5">Format: JPG, PNG, WebP. Maks 2MB.</p>
                                    @error('logo_perusahaan')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Divider --}}
                                <div class="border-t border-gray-100 my-5"></div>

                                {{-- Info --}}
                                <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3.5">
                                    <div class="flex items-start gap-2">
                                        <i class="fa-solid fa-circle-info text-blue-500 text-xs mt-0.5"></i>
                                        <p class="text-[11px] text-blue-700 leading-relaxed">
                                            Logo akan ditampilkan di <strong>kop surat PO</strong>, <strong>Surat
                                                Jalan</strong>, dan <strong>laporan cetak</strong>.
                                            Gunakan logo dengan latar transparan (PNG) untuk hasil terbaik.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SAVE BUTTON --}}
                    <div
                        class="mt-6 flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <p class="text-xs text-gray-400 hidden sm:block">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Pastikan data perusahaan yang Anda masukkan sudah benar sebelum menyimpan.
                        </p>
                        <button type="submit"
                            class="bg-gradient-to-r from-red-700 to-red-800 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-lg shadow-red-200 hover:from-red-800 hover:to-red-900 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            {{-- TAB CONTENT: PENGATURAN UMUM --}}
            <div x-show="activeTab === 'umum'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">

                <form action="{{ route('pengaturan.umum') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                        {{-- LEFT: Pajak --}}
                        <div class="lg:col-span-7">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-percent text-emerald-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-semibold text-gray-800">Pajak & Mata Uang</h2>
                                        <p class="text-[11px] text-gray-400">Konfigurasi pajak dan format mata uang</p>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    {{-- PPN Persen --}}
                                    <div>
                                        <label for="ppn_persen"
                                            class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                            Persentase PPN (%)
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                <i class="fa-solid fa-percent text-gray-300 text-sm"></i>
                                            </div>
                                            <input type="number" step="0.1" min="0" max="100" id="ppn_persen"
                                                name="ppn_persen"
                                                value="{{ old('ppn_persen', $settings['ppn_persen'] ?? '11') }}"
                                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200"
                                                placeholder="11">
                                        </div>
                                        @error('ppn_persen')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                        <p class="text-[11px] text-gray-400 mt-1.5">
                                            Nilai default PPN Indonesia: <strong>11%</strong>. Perubahan akan berlaku di
                                            seluruh dokumen.
                                        </p>
                                    </div>

                                    {{-- Mata Uang --}}
                                    <div>
                                        <label for="mata_uang"
                                            class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                            Simbol Mata Uang
                                        </label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                <i class="fa-solid fa-coins text-gray-300 text-sm"></i>
                                            </div>
                                            <input type="text" id="mata_uang" name="mata_uang"
                                                value="{{ old('mata_uang', $settings['mata_uang'] ?? 'Rp') }}"
                                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200"
                                                placeholder="Rp">
                                        </div>
                                        @error('mata_uang')
                                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: Info Card --}}
                        <div class="lg:col-span-5">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-circle-info text-amber-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-semibold text-gray-800">Informasi</h2>
                                        <p class="text-[11px] text-gray-400">Tentang pengaturan ini</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-3.5">
                                        <div class="flex items-start gap-2">
                                            <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xs mt-0.5"></i>
                                            <p class="text-[11px] text-amber-700 leading-relaxed">
                                                Perubahan <strong>persentase PPN</strong> akan mempengaruhi perhitungan
                                                pajak di
                                                seluruh dokumen: <strong>Purchase Order</strong>, <strong>Surat
                                                    Jalan</strong>,
                                                <strong>Approval</strong>, dan <strong>Laporan</strong>.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3.5">
                                        <div class="flex items-start gap-2">
                                            <i class="fa-solid fa-circle-info text-blue-500 text-xs mt-0.5"></i>
                                            <p class="text-[11px] text-blue-700 leading-relaxed">
                                                <strong>Simbol mata uang</strong> digunakan untuk format tampilan harga di
                                                seluruh aplikasi.
                                                Default: <strong>Rp</strong> (Rupiah Indonesia).
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Current Values --}}
                                    <div class="border-t border-gray-100 pt-4">
                                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Nilai
                                            Saat Ini</h3>
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-400 text-xs">PPN</span>
                                                <span
                                                    class="text-gray-700 font-mono text-xs bg-gray-50 px-2 py-0.5 rounded">{{ $settings['ppn_persen'] ?? '11' }}%</span>
                                            </div>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-400 text-xs">Mata Uang</span>
                                                <span
                                                    class="text-gray-700 font-mono text-xs bg-gray-50 px-2 py-0.5 rounded">{{ $settings['mata_uang'] ?? 'Rp' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SAVE BUTTON --}}
                    <div
                        class="mt-6 flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <p class="text-xs text-gray-400 hidden sm:block">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Perubahan pengaturan akan berlaku secara langsung di seluruh aplikasi.
                        </p>
                        <button type="submit"
                            class="bg-gradient-to-r from-red-700 to-red-800 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-lg shadow-red-200 hover:from-red-800 hover:to-red-900 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection