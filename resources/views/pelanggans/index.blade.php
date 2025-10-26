@extends('layouts.app')

@section('content')


<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-11 gap-4">
  <div class="relative col-span-5 text-gray-600">
    <input
      type="search"
      name="search"
      placeholder="Search"
      class="w-full h-10 px-5 text-sm border-0.5 outline-none focus:ring-0 rounded-xl">
    <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
      <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 56.966 56.966" width="16" height="16">
        <path
          d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  
             s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  
             c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z 
             M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  
             s-17-7.626-17-17S14.61,6,23.984,6z" />
      </svg>
    </button>
  </div>
</div>


<!-- TABEL -->

<div class="my-3">

  <div class="bg-white rounded-lg grid-cols-10">

    <div class="p-6">
      <!-- Header + Button -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Daftar Pelanggan</h2>
        <a href="{{ route('pelanggans.create') }}"
          class="bg-indigo-500 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-600">
          + Tambah Pelanggan Baru
        </a>
      </div>

      <table class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-lg">
        <thead class="text-xs text-gray-900 border-b-1 rounded-lg bg-neutral">
          <tr>
            <th scope="col" class="px-6 py-2 text-center">Kode Pelanggan</th>
            <th scope="col" class="px-6 py-2">Nama Pelanggan</th>
            <th scope="col" class="px-6 py-2">Alamat</th>
            <th scope="col" class="px-6 py-2">NPWP</th>
            <th scope="col" class="px-6 py-2">PIC</th>
            <th scope="col" class="px-6 py-2">Kategori Pelanggan</th>
            <th scope="col" class="px-6 py-2">Tipe Harga</th>
            <th scope="col" class="px-6 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($pelanggans as $pelanggan)
          <tr class=" text-left text-xs even:bg-violet-50 odd:bg-white">
            <td scope="row" class="px-6 py-2 text-center">{{ $pelanggan->pelanggan_id }}</td>
            <td scope="row" class="px-6 py-2">{{ $pelanggan->nama_pelanggan }}</td>
            <td scope="row" class="px-6 py-2">{{ $pelanggan->alamat}}</td>
            <td scope="row" class="px-6 py-2">{{ $pelanggan->NPWP }}</td>
            <td scope="row" class="px-6 py-2">{{ $pelanggan->PIC }}</td>
            <td scope="row" class="px-6 py-2">{{ $pelanggan->kategori_pelanggan->kategori_pelanggan ?? 'Belum ada kategori' }}</td>
            <td scope="row" class="px-6 py-2">{{ $pelanggan->tipe_harga ?? 'Belum ada ' }}</td>


            <!-- View -->
            <td class="text-center mx-2 my-2 flex justify-around items-center">
              <a href="{{ route('pelanggans.show', $pelanggan->pelanggan_id) }}"
                class="text-indigo-600 hover:text-indigo-900">
                <i class="fa-solid fa-eye cursor-pointer text-blue-700 mx-1"></i>
              </a>

              <!-- EDIT -->

              <a href="{{ route('pelanggans.edit', $pelanggan->pelanggan_id) }}"
                class="text-indigo-600 hover:text-indigo-900">
                <i class="fa-solid fa-pen cursor-pointer text-amber-300 mx-1"></i>
              </a>

              <!-- Delete -->
              <a>
                <form action="{{ route('pelanggans.destroy', $pelanggan->pelanggan_id) }}"
                  class=" w-0"
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus supplier ini?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-red-600 hover:text-red-700" title="Hapus">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>

</div>


@endsection