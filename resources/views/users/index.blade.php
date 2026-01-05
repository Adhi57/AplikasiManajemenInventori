@extends('layouts.app')
@section('page-title', 'Sistem / Manajemen User')
@section('content')

<div class="p-4 sm:p-6 bg-gray-50 rounded-xl shadow-inner min-h-[80vh]">

    <div class="grid grid-cols-12 gap-4 items-center mb-6">
        <div class="relative col-span-12 md:col-span-8 text-gray-600">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-3">
                <input
                    type="search"
                    name="search"
                    placeholder="Cari Nama atau Username..."
                    value="{{ request('search') }}"
                    class="flex-1 min-w-[200px] h-10 px-5 text-sm border border-gray-300 outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 rounded-xl transition duration-150 shadow-sm"
                >

                <select name="role" class="h-10 border border-gray-300 rounded-xl px-3 text-sm focus:ring-red-600 focus:border-red-600 shadow-sm outline-none">
                    <option value="">Semua Role</option>
                    @foreach (['SuperAdmin', 'Admin', 'Head', 'Staff'] as $role)
                        <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-xl shadow-lg hover:bg-red-800 transition duration-150 ease-in-out">
                    <i class="fa-solid fa-magnifying-glass"></i> Filter
                </button>
            </form>
        </div>
        <div class="col-span-12 md:col-span-4 flex justify-end">
            <a href="{{ route('users.create') }}"
                class="bg-red-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg hover:bg-red-800 transition duration-150 ease-in-out flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-sm"></i>
                Tambah User Baru
            </a>
        </div>
    </div>

    <div class="my-3">
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl border border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Daftar Pengguna Sistem</h2>

                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="text-xs text-white uppercase bg-red-700 border-b-4 border-red-500 sticky top-0 z-10">
                            <tr>
                                <th class="px-3 py-3 text-center rounded-tl-xl">ID User</th>
                                <th class="px-6 py-3">Nama Lengkap</th>
                                <th class="px-6 py-3">Username</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3 text-center">Role (Inline Edit)</th>
                                <th class="px-6 py-3 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr class="text-xs even:bg-red-50 odd:bg-white border-b hover:bg-red-100 transition duration-100">
                                <td class="px-3 py-2 font-semibold text-center text-gray-900">{{ $user->user_id }}</td>
                                <td class="px-6 py-2">{{ $user->nama_lengkap }}</td>
                                <td class="px-6 py-2 text-gray-500">{{ $user->username }}</td>
                                <td class="px-6 py-2">{{ $user->email }}</td>
                                <td class="px-6 py-2 text-center">
                                    {{-- Inline Edit Role --}}
                                    <select onchange="updateRole('{{ $user->user_id }}', this.value)" 
                                        class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full border-none focus:ring-0 cursor-pointer
                                        @if($user->role == 'SuperAdmin') bg-red-600 text-white
                                        @elseif($user->role == 'Admin') bg-blue-600 text-white
                                        @elseif($user->role == 'Head') bg-green-600 text-white
                                        @else bg-gray-600 text-white @endif">
                                        @foreach (['SuperAdmin', 'Admin', 'Head', 'Staff'] as $role)
                                            <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }} class="bg-white text-gray-800">
                                                {{ $role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-2 text-center whitespace-nowrap">
                                    <a href="{{ route('users.edit', $user->user_id) }}" class="inline-block text-gray-500 hover:text-amber-600 transition p-1">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    
                                    <button type="button" onclick="confirmDelete('{{ $user->user_id }}', '{{ $user->nama_lengkap }}')" class="text-gray-500 hover:text-red-700 transition p-1">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>

                                    <form id="delete-form-{{ $user->user_id }}" action="{{ route('users.destroy', $user->user_id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-4 border-t bg-gray-50">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Script SweetAlert2 & AJAX --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. Fitur Inline Update Role via AJAX
    function updateRole(userId, newRole) {
        Swal.fire({
            title: 'Ubah Role?',
            text: `Ubah role user ini menjadi ${newRole}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#b91c1c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Ubah!'
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
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                });
            } else {
                location.reload(); // Reset dropdown jika batal
            }
        });
    }

    // 2. Fitur Konfirmasi Hapus
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus User?',
            text: `Anda yakin ingin menghapus ${name}? Data transaksi terkait mungkin terdampak.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#b91c1c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Tetap!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    // Notifikasi Sukses dari Session
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