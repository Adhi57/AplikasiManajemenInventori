@extends('layouts.app')
@section('page-title', 'Pengaturan Profil')

@section('content')
<div class="p-4 sm:p-6 bg-gray-50 rounded-xl shadow-inner min-h-[80vh]">
    <div class="max-w-4xl mx-auto">
        
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            {{-- Header Profil --}}
            <div class="bg-gradient-to-r from-red-800 to-red-950 p-8 text-white">
                <div class="flex items-center gap-6">
                    <div class="h-24 w-24 bg-red-100 rounded-full flex items-center justify-center text-red-900 text-4xl font-bold shadow-lg border-4 border-red-700/50">
                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $user->nama_lengkap }}</h1>
                        <p class="opacity-80">{{ $user->role }} • {{ $user->username }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" 
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-600 outline-none transition">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-600 outline-none transition">
                    </div>

                    <div class="col-span-full border-t pt-4 mt-2">
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Ganti Password</h3>
                        <p class="text-sm text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="password" 
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-600 outline-none transition">
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-600 outline-none transition">
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-red-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-red-800 transition transform hover:scale-105 active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false
        });
    @endif
</script>
@endsection