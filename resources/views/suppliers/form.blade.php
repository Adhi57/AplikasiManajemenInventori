@php
    $isEdit = $supplier->exists;
    $actionRoute = $isEdit 
        ? route('suppliers.update', $supplier->id_supplier) 
        : route('suppliers.store');
@endphp

<form action="{{ $actionRoute }}" method="POST">
    @csrf

    {{-- method PUT untuk update --}}
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="mb-4">
        <label for="namaSupplier" class="block text-sm font-medium text-gray-700">Nama Supplier</label>
        <input type="text" name="namaSupplier" id="namaSupplier" 
               value="{{ old('namaSupplier', $supplier->namaSupplier ?? '') }}" 
               class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        @error('namaSupplier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label for="alamatSupplier" class="block text-sm font-medium text-gray-700">Alamat</label>
        <textarea name="alamatSupplier" id="alamatSupplier" 
               class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>{{ old('alamatSupplier', $supplier->alamatSupplier ?? '') }}</textarea>
        @error('alamatSupplier') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label for="Kota" class="block text-sm font-medium text-gray-700">Kota</label>
        <input type="text" name="Kota" id="Kota" 
               value="{{ old('Kota', $supplier->Kota ?? '') }}" 
               class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        @error('Kota') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="mb-4">
            <label for="noTelepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
            <input type="text" name="noTelepon" id="noTelepon" 
                   value="{{ old('noTelepon', $supplier->noTelepon ?? '') }}" 
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
            @error('noTelepon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label for="waktuPengiriman" class="block text-sm font-medium text-gray-700">Waktu Kirim (Hari)</label>
            <input type="number" name="waktuPengiriman" id="waktuPengiriman" 
                   value="{{ old('waktuPengiriman', $supplier->waktuPengiriman ?? '') }}" 
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
            @error('waktuPengiriman') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <div class="mt-6">
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">
            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Supplier' }}
        </button>
    </div>
</form>