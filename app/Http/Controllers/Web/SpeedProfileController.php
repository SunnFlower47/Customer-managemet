<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\SpeedProfile;
use App\Services\SpeedProfileService;
use Illuminate\Http\Request;

class SpeedProfileController extends BaseController
{
    protected $profileService;

    public function __construct(SpeedProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SpeedProfile::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $profiles = $query->orderBy('nama')->paginate(20)->appends($request->query());

        return view('speed-profiles.index', compact('profiles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'description' => 'nullable|string',
            'download_speed' => 'required|integer|min:1',
            'upload_speed' => 'required|integer|min:1',
            'profile_name' => 'nullable|string|max:255',
        ]);

        $this->profileService->createProfile($request->all());

        return $this->redirectToRouteWithParams('speed-profiles.index', $request, 'Speed profile berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpeedProfile $speedProfile)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'description' => 'nullable|string',
            'download_speed' => 'required|integer|min:1',
            'upload_speed' => 'required|integer|min:1',
            'profile_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $this->profileService->updateProfile($speedProfile, $request->all());

        return $this->redirectToRouteWithParams('speed-profiles.index', $request, 'Speed profile berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, SpeedProfile $speedProfile)
    {
        $this->profileService->deleteProfile($speedProfile);

        return $this->redirectToRouteWithParams('speed-profiles.index', $request, 'Speed profile berhasil dihapus.');
    }
}
