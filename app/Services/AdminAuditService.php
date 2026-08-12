<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdminAuditService
{
    /**
     * Log an event by the currently authenticated admin.
     */
    public static function log(
        string $event,
        ?string $description = null,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null
    ): ?AdminAuditLog {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return null;
        }

        $data = [
            'admin_id'    => $admin->id,
            'event'       => $event,
            'description' => $description,
        ];

        if ($auditable) {
            $data['auditable_type'] = get_class($auditable);
            $data['auditable_id']   = $auditable->getKey();
        }

        if ($oldValues !== null) {
            $data['old_values'] = $oldValues;
        }
        if ($newValues !== null) {
            $data['new_values'] = $newValues;
        }

        $req = $request ?? request();
        if ($req) {
            $data['ip_address'] = $req->ip();
            $data['user_agent'] = $req->userAgent();
        }

        return AdminAuditLog::create($data);
    }
}