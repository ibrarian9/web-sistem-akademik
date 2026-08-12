<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an audit log entry for system activities and model CRUD.
     */
    public static function log(string $event, string $description, $subject = null, array $extra = []): void
    {
        try {
            $user = Auth::user();
            $ipAddress = Request::ip() ?? '127.0.0.1';
            $userAgent = Request::userAgent() ?? 'CLI/System';

            $subjectType = null;
            $subjectId = null;
            $siswaId = $extra['siswa_id'] ?? null;

            if ($subject && is_object($subject)) {
                $subjectType = get_class($subject);
                $subjectId = method_exists($subject, 'getKey') ? $subject->getKey() : ($subject->id ?? null);
                
                if (!$siswaId && isset($subject->siswa_id)) {
                    $siswaId = $subject->siswa_id;
                }
            }

            DB::table('activity_log')->insert([
                'log_name' => $extra['log_name'] ?? 'default',
                'description' => $description,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'event' => $event,
                'causer_type' => $user ? get_class($user) : null,
                'causer_id' => $user ? $user->id : null,
                'siswa_id' => $siswaId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'attribute_changes' => isset($extra['changes']) ? json_encode($extra['changes']) : null,
                'properties' => json_encode(array_merge([
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ], $extra['properties'] ?? [])),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently log exception to laravel.log so DB error doesn't break main app logic
            logger()->error('AuditLogger error: ' . $e->getMessage());
        }
    }
}
