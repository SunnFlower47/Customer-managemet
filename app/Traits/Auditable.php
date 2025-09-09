<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the auditable trait
     */
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            $model->auditLog('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $model->auditLog('updated', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->auditLog('deleted', $model->getOriginal(), null);
        });
    }

    /**
     * Log audit trail
     */
    public function auditLog($action, $oldValues = null, $newValues = null, $description = null)
    {
        $user = Auth::user();
        
        AuditTrail::create([
            'user_type' => $user ? get_class($user) : null,
            'user_id' => $user ? $user->id : null,
            'event' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'tags' => $description ?: $this->getAuditDescription($action),
        ]);
    }

    /**
     * Get audit description
     */
    protected function getAuditDescription($action)
    {
        $modelName = class_basename($this);
        
        switch ($action) {
            case 'created':
                return "Created new {$modelName} record";
            case 'updated':
                return "Updated {$modelName} record";
            case 'deleted':
                return "Deleted {$modelName} record";
            default:
                return "Performed {$action} on {$modelName} record";
        }
    }

    /**
     * Get audit trails for this model
     */
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'record_id')
                    ->where('table_name', $this->getTable())
                    ->orderBy('created_at', 'desc');
    }
}
