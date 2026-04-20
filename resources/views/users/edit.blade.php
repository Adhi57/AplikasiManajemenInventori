@extends('layouts.app')
@section('page-title', 'Sistem / Edit User')
@section('content')

    <x-page-header title="Edit User" description="Perbarui informasi akun pengguna" icon="fa-user-pen">
        <x-slot name="actions">
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                <i class="fa-solid fa-arrow-left text-amber-400"></i>
                <span>Kembali</span>
            </a>
        </x-slot>
    </x-page-header>

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

    <form action="{{ route('users.update', $user->user_id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
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
                                <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
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
                                    Username
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-at absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" id="username" value="{{ $user->username }}"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                                        disabled>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1"><i class="fa-solid fa-lock text-[8px]"></i> Username tidak dapat diubah</p>
                            </div>
                            <div>
                                <label for="email"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
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
                                    Password Baru
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="password" name="password" id="password" placeholder="••••••••"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1"><i class="fa-solid fa-info-circle text-[8px]"></i> Kosongkan jika tidak ingin mengubah password</p>
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
                                            <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>{{ $role }}
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
                    {{-- User Info Card --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fa-solid fa-user text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $user->nama_lengkap }}</p>
                                <p class="text-[10px] text-gray-400">{{ $user->user_id }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 space-y-1">
                            <p><i class="fa-solid fa-at w-4 text-gray-400"></i> {{ $user->username }}</p>
                            <p><i class="fa-solid fa-envelope w-4 text-gray-400"></i> {{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 focus:ring-4 focus:ring-red-200">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
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
