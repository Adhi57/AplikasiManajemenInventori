<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
public function index(Request $request)
{
    $query = User::query();

    // Logika Search (jika ada)
    if ($request->has('search')) {
        $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
              ->orWhere('username', 'like', '%' . $request->search . '%');
    }

    // Tampilkan semua user kecuali diri sendiri dengan pagination
    // Ganti ->get() menjadi ->paginate(10)
    $users = $query->where('user_id', '!=', auth()->user()->user_id)
                   ->orderBy('role', 'asc')
                   ->paginate(10); // Menampilkan 10 data per halaman

    return view('users.index', compact('users'));
}
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'role'         => 'required|in:SuperAdmin,Admin,Head,Staff',
        ]);

        // Generate Custom ID (misal: USR-RANDOM)
        $userId = 'USR-' . strtoupper(bin2hex(random_bytes(3)));

        User::create([
            'user_id'      => $userId,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required',
            'role'         => 'required|in:SuperAdmin,Admin,Head,Staff',
            // Validasi unik kecuali user ini sendiri
            'email'        => 'required|email|unique:users,email,'.$user->user_id.',user_id',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'role'         => $request->role,
            'email'        => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user diperbarui');
    }

    public function destroy($id)
    {
        // PENCEGAHAN ERROR DATABASE (Foreign Key Constraints)
        // Cek apakah user memiliki transaksi di surat_jalans atau purchase_orders
        
        $hasTransactions = DB::table('surat_jalans')->where('user_id', $id)->exists() || 
                           DB::table('purchase_orders')->where('user_id', $id)->exists();

        if ($hasTransactions) {
            return back()->with('error', 'User tidak bisa dihapus karena memiliki riwayat transaksi (Surat Jalan/PO). Silakan non-aktifkan user saja (ubah password/email).');
        }

        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    public function updateRole(Request $request, $id)
{
    try {
        $user = User::findOrFail($id);
        
        // Validasi
        $request->validate([
            'role' => 'required|in:SuperAdmin,Admin,Head,Staff'
        ]);

        // Proteksi: Jangan biarkan superadmin mengubah role-nya sendiri jika dia admin terakhir
        if ($user->user_id === auth()->user()->user_id && $request->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'Anda tidak bisa menurunkan role Anda sendiri demi keamanan akses.'], 403);
        }

        $user->update(['role' => $request->role]);

        return response()->json([
            'success' => true, 
            'message' => 'Role ' . $user->nama_lengkap . ' berhasil diubah menjadi ' . $request->role
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Gagal mengubah role.'], 500);
    }
}
}
