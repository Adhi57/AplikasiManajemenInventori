@extends('layouts.app')
@section('page-title', 'Sistem / Riwayat User')
@section('content')

    <x-page-header title="Riwayat User" description="Daftar user yang telah dinonaktifkan (dihapus)" icon="fa-clock-rotate-left">
        <x-slot name="actions">
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                <i class="fa-solid fa-arrow-left text-amber-400"></i>
                <span>Kembali ke Users</span>
            </a>
        </x-slot>
    </x-page-header>

    {{-- Filters --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('users.trashed') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="search" name="search" placeholder="Cari nama atau username..." value="{{ request('search') }}"
                    class="w-full h-10 pl-9 pr-4 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm">
            </div>
            <div class="relative">
                <select name="role"
                    class="h-10 border border-gray-300 rounded-xl pl-3 pr-8 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm bg-white appearance-none">
                    <option value="">Semua Role</option>
                    @foreach (['SuperAdmin', 'Admin', 'Head', 'Staff'] as $role)
                        <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 h-10 px-4 bg-red-800 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition shadow-sm">
                <i class="fa-solid fa-filter text-xs"></i> Filter
            </button>
        </form>
    </div>

    {{-- Info Banner --}}
    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-amber-500 text-lg mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-800">Tentang Riwayat User</p>
            <p class="text-xs text-amber-600 mt-1">User yang dihapus akan dipindahkan ke sini. Anda dapat memulihkan akun atau menghapusnya secara permanen. User yang memiliki riwayat transaksi tidak dapat dihapus permanen.</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-trash-clock text-red-500"></i>
                User yang Dihapus
                @if($trashedUsers->total() > 0)
                    <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">{{ $trashedUsers->total() }} user</span>
                @endif
            </h3>
        </div>
        <div class="p-5">
            @if($trashedUsers->isEmpty())
                <div class="text-center py-16">
                    <i class="fa-solid fa-check-double text-5xl text-gray-200 mb-4"></i>
                    <p class="text-sm font-medium text-gray-400">Tidak ada user yang dihapus</p>
                    <p class="text-xs text-gray-300 mt-1">Semua akun pengguna masih aktif</p>
                </div>
            @else
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="text-xs text-white uppercase bg-red-800 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-3 text-center rounded-tl-xl">ID</th>
                                <th class="px-5 py-3">Nama Lengkap</th>
                                <th class="px-5 py-3">Username</th>
                                <th class="px-5 py-3">Email</th>
                                <th class="px-5 py-3 text-center">Role</th>
                                <th class="px-5 py-3 text-center">Dihapus Pada</th>
                                <th class="px-5 py-3 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trashedUsers as $user)
                                <tr class="text-xs border-b border-gray-100 even:bg-gray-50/50 hover:bg-red-50 transition duration-100">
                                    <td class="px-3 py-2.5 font-semibold text-center text-gray-900">{{ $user->user_id }}</td>
                                    <td class="px-5 py-2.5 font-medium">
                                        <div class="flex items-center gap-2">
                                            <span class="line-through text-gray-400">{{ $user->nama_lengkap }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-2.5 text-gray-500">{{ $user->username }}</td>
                                    <td class="px-5 py-2.5">{{ $user->email }}</td>
                                    <td class="px-5 py-2.5 text-center">
                                        @php
                                            $roleBadge = match ($user->role) {
                                                'SuperAdmin' => 'bg-red-100 text-red-700',
                                                'Admin' => 'bg-blue-100 text-blue-700',
                                                'Head' => 'bg-emerald-100 text-emerald-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            };
                                        @endphp
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full {{ $roleBadge }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2.5 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <span class="font-medium">{{ $user->deleted_at->format('d M Y') }}</span>
                                            <span class="text-[10px]">{{ $user->deleted_at->format('H:i') }} WIB</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-2.5 text-center whitespace-nowrap">
                                        <button type="button"
                                            onclick="confirmRestore('{{ $user->user_id }}', '{{ $user->nama_lengkap }}')"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition"
                                            title="Pulihkan">
                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                        </button>
                                        <button type="button"
                                            onclick="confirmForceDelete('{{ $user->user_id }}', '{{ $user->nama_lengkap }}')"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                            title="Hapus Permanen">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>

                                        {{-- Restore Form --}}
                                        <form id="restore-form-{{ $user->user_id }}"
                                            action="{{ route('users.restore', $user->user_id) }}" method="POST" class="hidden">
                                            @csrf @method('PATCH')
                                        </form>

                                        {{-- Force Delete Form --}}
                                        <form id="force-delete-form-{{ $user->user_id }}"
                                            action="{{ route('users.forceDelete', $user->user_id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($trashedUsers->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                {{ $trashedUsers->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function confirmRestore(id, name) {
                Swal.fire({
                    title: 'Pulihkan User?',
                    text: `Anda yakin ingin memulihkan akun "${name}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pulihkan!',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`restore-form-${id}`).submit();
                    }
                });
            }

            function confirmForceDelete(id, name) {
                Swal.fire({
                    title: 'Hapus Permanen?',
                    html: `<p>Anda yakin ingin menghapus <strong>"${name}"</strong> secara permanen?</p><p class="text-xs text-red-500 mt-2"><i class="fa-solid fa-triangle-exclamation"></i> Tindakan ini TIDAK BISA dibatalkan!</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus Permanen!',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`force-delete-form-${id}`).submit();
                    }
                });
            }
        </script>
    @endpush

@endsection
