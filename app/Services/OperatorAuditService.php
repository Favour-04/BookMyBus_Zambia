<?php

namespace App\Services;

use App\Models\Operator;
use App\Models\OperatorAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OperatorAuditService
{
    /**
     * Log an event by the currently authenticated operator.
     */
    public static function log(
        string $event,
        ?string $description = null,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null
    ): OperatorAuditLog {
        $operator = self::getOperator();

        if (!$operator) {
            // Cannot log without an operator context
            return new OperatorAuditLog();
        }

        $data = [
            'operator_id' => $operator->id,
            'event' => $event,
            'description' => $description,
        ];

        if ($auditable) {
            $data['auditable_type'] = get_class($auditable);
            $data['auditable_id'] = $auditable->getKey();
        }

        if ($oldValues !== null) {
            $data['old_values'] = $oldValues;
        }
        if ($newValues !== null) {
            $data['new_values'] = $newValues;
        }

        if ($request) {
            $data['ip_address'] = $request->ip();
            $data['user_agent'] = $request->userAgent();
        } elseif (request()) {
            $data['ip_address'] = request()->ip();
            $data['user_agent'] = request()->userAgent();
        }

        return OperatorAuditLog::create($data);
    }

    /**
     * Log that a specific model was viewed.
     */
    public static function viewed(Model $auditable, string $description = null): ?OperatorAuditLog
    {
        $type = class_basename($auditable);
        $event = strtolower($type) . '.viewed';
        $desc = $description ?? "Viewed {$type} #{$auditable->getKey()}";
        return self::log($event, $desc, $auditable);
    }

    /**
     * Log that a model was created.
     */
    public static function created(Model $auditable, string $description = null): ?OperatorAuditLog
    {
        $type = class_basename($auditable);
        $event = strtolower($type) . '.created';
        $desc = $description ?? "Created {$type} #{$auditable->getKey()}";
        return self::log($event, $desc, $auditable, null, $auditable->toArray());
    }

    /**
     * Log that a model was updated.
     */
    public static function updated(Model $auditable, array $original, string $description = null): ?OperatorAuditLog
    {
        $type = class_basename($auditable);
        $event = strtolower($type) . '.updated';
        $desc = $description ?? "Updated {$type} #{$auditable->getKey()}";
        return self::log($event, $desc, $auditable, $original, $auditable->getChanges());
    }

    /**
     * Log that a model was deleted/cancelled.
     */
    public static function deleted(Model $auditable, string $description = null): ?OperatorAuditLog
    {
        $type = class_basename($auditable);
        $event = strtolower($type) . '.cancelled';
        $desc = $description ?? "Cancelled {$type} #{$auditable->getKey()}";
        return self::log($event, $desc, $auditable, $auditable->toArray());
    }

    /**
     * Get the authenticated operator from any guard or session fallback.
     */
    private static function getOperator(): ?Operator
    {
        if ($operator = auth()->guard('operator')->user()) {
            return $operator;
        }
        if (session('operator_id')) {
            return Operator::find(session('operator_id'));
        }
        return null;
    }
}