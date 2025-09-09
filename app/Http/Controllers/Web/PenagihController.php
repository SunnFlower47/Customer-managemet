<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Penagih;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenagihController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penagihs = Penagih::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('penagihs.index', compact('penagihs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('penagihs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:penagihs,email',
                'no_hp' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'create_user_account' => 'nullable|boolean',
                'user_name' => 'required_if:create_user_account,1|nullable|string|max:255',
                'user_email' => 'required_if:create_user_account,1|nullable|email|unique:users,email',
                'user_password' => 'required_if:create_user_account,1|nullable|string|min:6',
                'aktif' => 'nullable|boolean'
            ]);

        $penagih = Penagih::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'aktif' => $request->has('aktif') ? true : false,
        ]);

        // Create user account if requested
        if ($request->has('create_user_account') && $request->create_user_account) {
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->user_password),
                'role' => 'penagih',
                'aktif' => true,
            ]);

            $penagih->update(['user_id' => $user->id]);
        }

            return $this->redirectToRouteWithParams('penagihs.index', $request, 'Penagih berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penagih $penagih)
    {
        $penagih->load(['user', 'pelanggans', 'pembayarans']);
        return view('penagihs.show', compact('penagih'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penagih $penagih)
    {
        return view('penagihs.edit', compact('penagih'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penagih $penagih)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:penagihs,email,' . $penagih->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'aktif' => 'boolean'
        ]);

        $penagih->update($request->all());

        return $this->redirectToRouteWithParams('penagihs.index', $request, 'Penagih berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Penagih $penagih)
    {
        $penagih->delete();

        return $this->redirectToRouteWithParams('penagihs.index', $request, 'Penagih berhasil dihapus.');
    }
}
