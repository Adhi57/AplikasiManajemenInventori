@extends('layouts.app')
@section('page-title', 'Pengaturan Profil')

@section('content')
    <div class="p-4 sm:p-6 bg-gray-50 rounded-xl min-h-[80vh]">
        <div class="max-w-5xl mx-auto">

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

            {{-- PROFILE HEADER CARD --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="relative">
                    {{-- Cover / Banner --}}
                    <div class="h-36 bg-gradient-to-br from-red-700 via-red-800 to-red-950 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 800 200" preserveAspectRatio="none">
                                <path d="M0,100 C150,200 350,0 500,100 C650,200 750,50 800,100 L800,200 L0,200 Z"
                                    fill="white" />
                            </svg>
                        </div>
                        <div class="absolute top-4 right-5 flex items-center gap-2">
                            <span
                                class="text-[11px] text-red-200 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/10">
                                <i class="fa-solid fa-shield-halved mr-1"></i> {{ $user->role }}
                            </span>
                        </div>
                    </div>

                    {{-- Avatar & Name (overlapping banner) --}}
                    <div class="px-6 sm:px-8 pb-6 -mt-10 relative z-10">
                        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 sm:gap-6">
                            {{-- Avatar --}}
                            <div
                                class="w-24 h-24 bg-white rounded-2xl shadow-lg border-4 border-white flex items-center justify-center text-red-700 text-3xl font-bold shrink-0">
                                {{ strtoupper(substr($user->nama_lengkap, 0, 2)) }}
                            </div>
                            {{-- Info --}}
                            <div class="pb-1 flex-1 min-w-0">
                                <h1 class="text-xl font-bold text-gray-900 truncate">{{ $user->nama_lengkap }}</h1>
                                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                            </div>
                            {{-- Quick Info Badges --}}
                            <div class="hidden sm:flex items-center gap-2 pb-1">
                                <span
                                    class="text-xs text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                                    <i class="fa-solid fa-user-tag text-gray-400 mr-1"></i> {{ $user->username }}
                                </span>
                                <span
                                    class="text-xs text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                                    <i class="fa-solid fa-calendar text-gray-400 mr-1"></i> Bergabung
                                    {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    {{-- LEFT COLUMN: Informasi Pribadi --}}
                    <div class="lg:col-span-7">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-user-pen text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-800">Informasi Pribadi</h2>
                                    <p class="text-[11px] text-gray-400">Ubah nama dan alamat email Anda</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                {{-- Nama Lengkap --}}
                                <div>
                                    <label for="nama_lengkap"
                                        class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                        Nama Lengkap
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-user text-gray-300 text-sm"></i>
                                        </div>
                                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                                            value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200"
                                            placeholder="Masukkan nama lengkap">
                                    </div>
                                    @error('nama_lengkap')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="email"
                                        class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                        Alamat Email
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-envelope text-gray-300 text-sm"></i>
                                        </div>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:bg-white outline-none transition-all duration-200"
                                            placeholder="contoh@email.com">
                                    </div>
                                    @error('email')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Username (Read-only) --}}
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                        Username
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-at text-gray-300 text-sm"></i>
                                        </div>
                                        <input type="text" value="{{ $user->username }}" disabled
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-100 bg-gray-100/80 text-sm text-gray-500 cursor-not-allowed">
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Username tidak dapat diubah</p>
                                </div>

                                {{-- Role (Read-only) --}}
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                        Peran / Role
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-shield-halved text-gray-300 text-sm"></i>
                                        </div>
                                        <input type="text" value="{{ $user->role }}" disabled
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-100 bg-gray-100/80 text-sm text-gray-500 cursor-not-allowed">
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Peran hanya bisa diubah oleh Administrator</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Keamanan --}}
                    <div class="lg:col-span-5">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                                    <i class="fa-solid fa-lock text-amber-600 text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-800">Keamanan Akun</h2>
                                    <p class="text-[11px] text-gray-400">Ganti password akun Anda</p>
                                </div>
                            </div>

                            <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-3.5 mb-5">
                                <div class="flex items-start gap-2">
                                    <i class="fa-solid fa-circle-info text-amber-500 text-xs mt-0.5"></i>
                                    <p class="text-[11px] text-amber-700 leading-relaxed">
                                        Kosongkan kedua field di bawah ini jika Anda <strong>tidak ingin</strong> mengubah
                                        password. Password minimal 8 karakter.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                {{-- Password Baru --}}
                                <div>
                                    <label for="password"
                                        class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                        Password Baru
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-key text-gray-300 text-sm"></i>
                                        </div>
                                        <input type="password" id="password" name="password"
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 focus:bg-white outline-none transition-all duration-200"
                                            placeholder="Minimal 8 karakter">
                                    </div>
                                    @error('password')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Konfirmasi Password --}}
                                <div>
                                    <label for="password_confirmation"
                                        class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wider">
                                        Konfirmasi Password
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-check-double text-gray-300 text-sm"></i>
                                        </div>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 text-sm text-gray-800 placeholder-gray-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100 focus:bg-white outline-none transition-all duration-200"
                                            placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 my-6"></div>

                            {{-- Info Akun --}}
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Informasi Akun
                                </h3>
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-400 text-xs">User ID</span>
                                        <span
                                            class="text-gray-700 font-mono text-xs bg-gray-50 px-2 py-0.5 rounded">{{ $user->user_id }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-400 text-xs">Dibuat</span>
                                        <span
                                            class="text-gray-700 text-xs">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y, H:i') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-400 text-xs">Diperbarui</span>
                                        <span
                                            class="text-gray-700 text-xs">{{ $user->updated_at ? $user->updated_at->translatedFormat('d M Y, H:i') : '-' }}</span>
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
                        Pastikan data yang Anda masukkan sudah benar sebelum menyimpan.
                    </p>
                    <button type="submit"
                        class="bg-gradient-to-r from-red-700 to-red-800 text-white px-8 py-3 rounded-xl font-semibold text-sm shadow-lg shadow-red-200 hover:from-red-800 hover:to-red-900 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection