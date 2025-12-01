<?php

namespace App\Observers;

use App\Models\Odp;

class OdpObserver
{
    /**
     * Handle the Odp "created" event.
     */
    public function created(Odp $odp): void
    {
        // Update port terpakai parent ODP jika ada
        if ($odp->parentOdp) {
            $odp->parentOdp->syncPortTerpakai();
            $odp->parentOdp->updateParentPortTerpakai();
        }
    }

    /**
     * Handle the Odp "updated" event.
     */
    public function updated(Odp $odp): void
    {
        // Handle perubahan parent_odp_id
        if ($odp->isDirty('parent_odp_id')) {
            $oldParentId = $odp->getOriginal('parent_odp_id');
            $newParentId = $odp->parent_odp_id;

            // Update parent lama (jika ada)
            if ($oldParentId) {
                $oldParent = Odp::find($oldParentId);
                if ($oldParent) {
                    $oldParent->syncPortTerpakai();
                    $oldParent->updateParentPortTerpakai();
                }
            }

            // Update parent baru (jika ada)
            if ($newParentId) {
                $newParent = Odp::find($newParentId);
                if ($newParent) {
                    $newParent->syncPortTerpakai();
                    $newParent->updateParentPortTerpakai();
                }
            }
        }

        // Sync port terpakai ODP ini
        $odp->syncPortTerpakai();
    }

    /**
     * Handle the Odp "deleted" event.
     */
    public function deleted(Odp $odp): void
    {
        // Update port terpakai parent ODP setelah delete
        if ($odp->parentOdp) {
            $odp->parentOdp->syncPortTerpakai();
            $odp->parentOdp->updateParentPortTerpakai();
        }
    }

    /**
     * Handle the Odp "restored" event.
     */
    public function restored(Odp $odp): void
    {
        //
    }

    /**
     * Handle the Odp "force deleted" event.
     */
    public function forceDeleted(Odp $odp): void
    {
        //
    }
}
