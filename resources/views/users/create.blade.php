@extends('layouts.app')
@section('page-title', 'Sistem / Tambah User')
@section('content')

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-800 flex items-center justify-center shadow">
                    <i class="fa-solid fa-user-plus text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah User Baru</h1>
                    <p class="text-sm text-gray-500">Silakan isi formulir untuk mendaftarkan akun baru</p>
                </div>
            </div>
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="mb-6 bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-red-50 border-b border-red-200">
                <h3 class="font-bold text-red-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Terdapat Kesalahan
                </h3>
            </div>
            <div class="p-5">
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                {{-- Section 1: Informasi Akun --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-user-gear text-red-500"></i>
                            Informasi Akun
                        </h3>
                    </div>
                    <div class="p-5 space-y-5">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="nama_lengkap"
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i
                                    class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                    required>
                            </div>
                        </div>

                        {{-- Username & Email --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="username"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Username <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-at absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                                        placeholder="budisantoso123"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                            <div>
                                <label for="email"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        placeholder="name@company.com"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Password & Role --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-red-500"></i>
                            Keamanan & Hak Akses
                        </h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="password" name="password" id="password" placeholder="••••••••"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                            <div>
                                <label for="role"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-user-shield absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <select name="role" id="role"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none"
                                        required>
                                        <option value="">-- Pilih Role --</option>
                                        @foreach(['SuperAdmin', 'Admin', 'Head', 'Staff'] as $role)
                                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== RIGHT COLUMN: Actions ===== --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 focus:ring-4 focus:ring-red-200">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan User
                        </button>
                        <a href="{{ route('users.index') }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                            <i class="fa-solid fa-xmark"></i>
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection