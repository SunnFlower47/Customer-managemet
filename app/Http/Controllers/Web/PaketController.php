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
        return view('pakets.index', compact('pakets'));
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
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        Paket::create($request->all());

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
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        $paket->update($request->all());

        return $this->redirectToRouteWithParams('pakets.index', $request, 'Paket berhasil diperbarui.');
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
