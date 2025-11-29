<?php

namespace App\Services;

use App\Models\SpeedProfile;
use Illuminate\Support\Facades\Log;

class SpeedProfileService
{
    /**
     * Get all active speed profiles
     */
    public function getActiveProfiles()
    {
        return SpeedProfile::active()->orderBy('nama')->get();
    }

    /**
     * Create speed profile
     */
    public function createProfile(array $data): SpeedProfile
    {
        return SpeedProfile::create([
            'nama' => $data['nama'],
            'description' => $data['description'] ?? null,
            'download_speed' => $data['download_speed'],
            'upload_speed' => $data['upload_speed'],
            'profile_name' => $data['profile_name'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update speed profile
     */
    public function updateProfile(SpeedProfile $profile, array $data): SpeedProfile
    {
        $profile->update($data);
        return $profile->fresh();
    }

    /**
     * Delete speed profile
     */
    public function deleteProfile(SpeedProfile $profile): bool
    {
        return $profile->delete();
    }

    /**
     * Get profile by ID
     */
    public function getProfileById(int $profileId): ?SpeedProfile
    {
        return SpeedProfile::find($profileId);
    }
}

