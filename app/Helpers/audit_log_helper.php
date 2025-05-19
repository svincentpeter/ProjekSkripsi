<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

if (!function_exists('audit_log')) {
    /**
     * Audit log global helper
     * 
     * @param string $aksi        (aksi, misal: 'create', 'update', 'delete')
     * @param string $modelType   (nama model/table, misal: 'penarikan')
     * @param int $modelId        (id model/table terkait)
     * @param array|null $oldData (data lama, bisa null)
     * @param array|null $newData (data baru, bisa null)
     * @param int|null $userId    (user id, default Auth::id())
     */
    function audit_log($aksi, $modelType, $modelId, $oldData = null, $newData = null, $userId = null)
    {
        DB::table('audit_logs')->insert([
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'aksi'       => $aksi,
            'old_data'   => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'new_data'   => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'user_id'    => $userId ?? Auth::id(),
            'ip'         => Request::ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
