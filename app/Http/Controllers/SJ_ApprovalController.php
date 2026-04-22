<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SJ_ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search'); // ambil keyword pencarian
    
        $suratJalans = SuratJalan::with(['details', 'pelanggan', 'user'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sj_id', 'like', "%{$search}%")
                      ->orWhereHas('pelanggan', function ($pelangganQuery) use ($search) {
                          $pelangganQuery->where('nama_pelanggan', 'like', "%{$search}%");
                      })
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('nama_lengkap', 'like', "%{$search}%");
                      });
                });
            })
            ->orderByRaw("
                CASE 
                    WHEN status = 'Pending' THEN 1
                    WHEN status = 'Disetujui' THEN 2
                    WHEN status = 'Ditolak' THEN 3
                    WHEN status = 'Dikirim' THEN 4
                    WHEN status = 'Selesai' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('approval.approval_surat_jalan', compact('suratJalans', 'status', 'search'));
    }
    

    public function show($sj_id)
    {
        $suratJalan = SuratJalan::with(['details.barang', 'pelanggan', 'user'])
            ->where('sj_id', $sj_id)
            ->firstOrFail();

        return view('approval.show_surat_jalan', compact('suratJalan'));
    }


    public function approve($sj_id)
    {
        DB::beginTransaction();
        try {
            $suratJalan = SuratJalan::findOrFail($sj_id);
            $suratJalan->status = 'Disetujui';
            $suratJalan->approved_by = \Illuminate\Support\Facades\Auth::user()->user_id ?? \Illuminate\Support\Facades\Auth::id();
            $suratJalan->save();

            DB::commit();
            return redirect()
                ->route('approval.approval_surat_jalan')
                ->with('success', 'Surat Jalan berhasil disetujui.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui surat jalan: ' . $e->getMessage());
        }
    }

    public function reject($sj_id)
    {
        DB::beginTransaction();
        try {
            $suratJalan = SuratJalan::findOrFail($sj_id);
            $suratJalan->status = 'Ditolak';
            $suratJalan->save();

            DB::commit();
            return redirect()
                ->route('approval.approval_surat_jalan')
                ->with('success', 'Surat Jalan berhasil ditolak.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak surat jalan: ' . $e->getMessage());
        }
    }

    public function print_sj($sj_id)
    {
        $suratJalan = SuratJalan::with(['details.barang', 'pelanggan', 'user', 'approver'])
            ->where('sj_id', $sj_id)
            ->firstOrFail();

        return Pdf::loadView('approval.print_sj', compact('suratJalan'))
            ->setPaper('a4', 'portrait')
            ->stream('SuratJalan_' . $sj_id . '.pdf');
    }

}
