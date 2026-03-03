# Rencana Redesign Detail Barang

Halaman detail barang saat ini cukup kaku. Rencanya adalah:

1. **Header**: Mengubah format header menggunakan _card_ yang memanjang berisi nama barang, kode barang, label status (misalnya stok aman atau kurang), dan foto barang di sebelah kiri.
2. **Tab Interface (Navigasi dalam Halaman)**: Supaya lebih interaktif dan rapi, informasi akan dibagi menjadi beberapa _tab_ menggunakan Alpine.js:
    - **Tab Informasi Umum**: Menampilkan Kategori, Supplier, Satuan Jual, dan Isi per Karton menggunakan _grid_ modern dengan ikon (FontAwesome).
    - **Tab Harga**: Memisahkan Harga Beli, Harga Jual, Tipe Harga, dan Periode Berlaku menggunakan _Card Stats_ yang elegan.
    - **Tab Stok & Inventori**: Menampilkan sisa stok (karton maupun _pcs_) dan stok rusak secara lebih menarik visualisasinya dengan _progress bar_ atau _badge_. Serta Tanggal Kadaluarsa.
3. **Typography & Styling**: Memanfaatkan Tailwind CSS yang sudah ada untuk memperindah _border_, _shadow_, _hover state_, dan hierarki visual teks sehingga lebih informatif.

File yang akan diedit: `resources/views/barangs/show.blade.php`
