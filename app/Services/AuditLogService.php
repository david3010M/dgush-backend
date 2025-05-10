<?php
namespace App\Services;

use App\Models\CulquiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogService
{

    public static function log(string $action, $requestData, $responseData = null): void
    {
        CulquiLog::create([
            'action'     => $action,
            'request'    => is_array($requestData) ? json_encode($requestData) : $requestData,
            'response'   => is_array($responseData) ? json_encode($responseData) : $responseData,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
