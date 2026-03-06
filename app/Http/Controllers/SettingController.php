<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = Setting::getAllAsArray();

        return view('settings.index', compact('settings'));
    }

    /**
     * Update company profile settings.
     */
    public function updateCompanyProfile(Request $request)
    {
        $request->validate([
            'nama_perusahaan'    => 'required|string|max:255',
            'alamat_perusahaan'  => 'required|string|max:500',
            'telepon_perusahaan' => 'required|string|max:50',
            'email_perusahaan'   => 'required|email|max:255',
            'logo_perusahaan'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        Setting::set('nama_perusahaan', $request->nama_perusahaan);
        Setting::set('alamat_perusahaan', $request->alamat_perusahaan);
        Setting::set('telepon_perusahaan', $request->telepon_perusahaan);
        Setting::set('email_perusahaan', $request->email_perusahaan);

        if ($request->hasFile('logo_perusahaan')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('logo_perusahaan');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo_perusahaan')->store('logos', 'public');
            Setting::set('logo_perusahaan', $path);
        }

        return back()->with('success', 'Profil perusahaan berhasil diperbarui!');
    }

    /**
     * Update general settings.
     */
    public function updateGeneralSettings(Request $request)
    {
        $request->validate([
            'ppn_persen'       => 'required|numeric|min:0|max:100',
            'mata_uang'        => 'required|string|max:10',
            'kapasitas_gudang' => 'required|numeric|min:1',
        ]);

        Setting::set('ppn_persen', $request->ppn_persen);
        Setting::set('mata_uang', $request->mata_uang);
        Setting::set('kapasitas_gudang', $request->kapasitas_gudang);

        return back()->with('success', 'Pengaturan umum berhasil diperbarui!');
    }
}
