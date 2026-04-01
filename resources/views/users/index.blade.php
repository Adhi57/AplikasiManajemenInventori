@extends('layouts.app')
@section('page-title', 'Sistem / Manajemen User')
@section('content')

    <x-page-header title="Manajemen User" description="Kelola pengguna dan hak akses sistem" icon="fa-users-gear">
    <x-slot name="actions">
        <a href="{{ route('users.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah User Baru</span>
        </a>
    </x-slot>
</x-page-header>

    {{-- Filters --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-3">
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

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-table-list text-red-500"></i>
                Daftar Pengguna Sistem
            </h3>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-white uppercase bg-red-800 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-3 text-center rounded-tl-xl">ID</th>
                            <th class="px-5 py-3">Nama Lengkap</th>
                            <th class="px-5 py-3">Username</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3 text-center">Role</th>
                            <th class="px-5 py-3 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr
                                class="text-xs border-b border-gray-100 even:bg-gray-50/50 hover:bg-red-50 transition duration-100">
                                <td class="px-3 py-2.5 font-semibold text-center text-gray-900">{{ $user->user_id }}</td>
                                <td class="px-5 py-2.5 font-medium">{{ $user->nama_lengkap }}</td>
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
                                    <select onchange="updateRole('{{ $user->user_id }}', this.value)"
                                        class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full border-2 border-transparent focus:ring-2 focus:ring-red-200 cursor-pointer transition {{ $roleBadge }}">
                                        @foreach (['SuperAdmin', 'Admin', 'Head', 'Staff'] as $role)
                                            <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}
                                                class="bg-white text-gray-800">{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-5 py-2.5 text-center whitespace-nowrap">
                                    <a href="{{ route('users.edit', $user->user_id) }}"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                        title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <button type="button"
                                        onclick="confirmDelete('{{ $user->user_id }}', '{{ $user->nama_lengkap }}')"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                        title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                    <form id="delete-form-{{ $user->user_id }}"
                                        action="{{ route('users.destroy', $user->user_id) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $users->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            function updateRole(userId, newRole) {
                Swal.fire({
                    title: 'Ubah Role?',
                    text: `Ubah role user ini menjadi ${newRole}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/users/${userId}/update-role`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ role: newRole })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success', title: 'Berhasil!', text: data.message,
                                        timer: 2000, showConfirmButton: false, timerProgressBar: true,
                                        customClass: { popup: 'swal-custom-popup swal-success-popup' },
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message, customClass: { popup: 'swal-custom-popup' } });
                                }
                            })
                            .catch(() => {
                                Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan sistem.', customClass: { popup: 'swal-custom-popup' } });
                            });
                    } else {
                        location.reload();
                    }
                });
            }

            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Hapus User?',
                    text: `Anda yakin ingin menghapus ${name}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                });
            }
        </script>
    @endpush

@endsection