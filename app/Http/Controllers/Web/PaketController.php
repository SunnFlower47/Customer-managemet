<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pakets = Paket::orderBy('created_at', 'desc')->paginate(20);

        // Hitung ringkasan pajak dari seluruh pelanggan dengan status Aktif dan Bayar Double
        $taxSummary = \App\Models\Pelanggan::whereIn('status', ['aktif', 'bayar double'])
            ->join('pakets', 'pelanggans.paket_id', '=', 'pakets.id')
            ->selectRaw('
                SUM(pakets.ppn_nominal) as total_ppn,
                SUM(pakets.bhp_nominal) as total_bhp,
                SUM(pakets.uso_nominal) as total_uso,
                SUM(pakets.adm_nominal) as total_adm
            ')
            ->first();

        $stats = [
            'total_ppn' => $taxSummary->total_ppn ?? 0,
            'total_bhp' => $taxSummary->total_bhp ?? 0,
            'total_uso' => $taxSummary->total_uso ?? 0,
            'total_adm' => $taxSummary->total_adm ?? 0,
            'grand_total_pajak' => ($taxSummary->total_ppn ?? 0) + ($taxSummary->total_bhp ?? 0) + ($taxSummary->total_uso ?? 0) + ($taxSummary->total_adm ?? 0)
        ];

        return view('pakets.index', compact('pakets', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pakets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        $data = $request->all();
        $data = $this->calculateTaxes($data);

        Paket::create($data);

        return $this->redirectToRouteWithParams('pakets.index', $request, 'Paket berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paket $paket)
    {
        // Paginate pelanggan yang menggunakan paket ini
        $pelanggans = $paket->pelanggans()->with('penagih')->paginate(10);

        return view('pakets.show', compact('paket', 'pelanggans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paket $paket)
    {
        return view('pakets.edit', compact('paket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paket $paket)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:255',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        $data = $request->all();
        $data = $this->calculateTaxes($data);

        $paket->update($data);

        return $this->redirectToRouteWithParams('pakets.index', $request, 'Paket berhasil diperbarui.');
    }

    /**
     * Calculate taxes based on harga_dasar
     */
    private function calculateTaxes(array $data): array
    {
        if (isset($data['harga_dasar']) && $data['harga_dasar'] > 0) {
            $companyProfile = \App\Models\CompanyProfile::first();
            $ppn = $companyProfile->ppn_persen ?? 11.0;
            $bhp = $companyProfile->bhp_persen ?? 0.5;
            $uso = $companyProfile->uso_persen ?? 1.25;
            $adm = $companyProfile->adm_persen ?? 2.5;

            $data['ppn_nominal'] = $data['harga_dasar'] * ($ppn / 100);
            $data['bhp_nominal'] = $data['harga_dasar'] * ($bhp / 100);
            $data['uso_nominal'] = $data['harga_dasar'] * ($uso / 100);
            $data['adm_nominal'] = $data['harga_dasar'] * ($adm / 100);
            
            // Total harga (dibulatkan untuk menghindari masalah desimal di UI)
            $data['harga'] = round($data['harga_dasar'] + $data['ppn_nominal'] + $data['bhp_nominal'] + $data['uso_nominal'] + $data['adm_nominal']);
        } else {
            $data['harga_dasar'] = 0;
            $data['ppn_nominal'] = 0;
            $data['bhp_nominal'] = 0;
            $data['uso_nominal'] = 0;
            $data['adm_nominal'] = 0;
            $data['harga'] = 0;
        }

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Paket $paket)
    {
        $paket->delete();

        return $this->redirectToRouteWithParams('pakets.index', $request, 'Paket berhasil dihapus.');
    }
}
