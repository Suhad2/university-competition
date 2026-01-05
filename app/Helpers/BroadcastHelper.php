<?php

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\Log;

/**
 * التحقق من إمكانية الاتصال بـ Pusher
 */
function canConnectToPusher(int $timeout = null): bool
{
    try {
        $hosts = [
            'mt1' => 'api-mt1.pusher.com',
            'us2' => 'api-us2.pusher.com',
            'eu'  => 'api-eu.pusher.com',
        ];
        
        $cluster = config('broadcasting.pusher.options.cluster', 'mt1');
        $host = $hosts[$cluster] ?? 'api-mt1.pusher.com';
        
        // استخدام timeout من المدخلات أو من البيئة
        $timeout = $timeout ?? (env('APP_ENV') === 'production' ? 5 : 3);
        
        $socket = @fsockopen($host, 443, $errno, $errstr, $timeout);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    } catch (\Exception $e) {
        Log::warning('Pusher connection check failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * بث الحدث بشكل آمن مع التحقق من الاتصال
 */
function broadcastSafe($event): bool
{
    if (!canConnectToPusher()) {
        Log::warning('Skipping Pusher broadcast - no connection available', [
            'event' => get_class($event),
            'time' => now()->toIso8601String(),
        ]);
        return false;
    }

    try {
        broadcast($event);
        Log::info('Pusher broadcast sent successfully', [
            'event' => get_class($event),
            'time' => now()->toIso8601String(),
        ]);
        return true;
    } catch (BroadcastException $e) {
        Log::error('Pusher broadcast failed', [
            'event' => get_class($event),
            'error' => $e->getMessage(),
            'time' => now()->toIso8601String(),
        ]);
        return false;
    } catch (\Exception $e) {
        Log::error('Unexpected error during Pusher broadcast', [
            'event' => get_class($event),
            'error' => $e->getMessage(),
            'time' => now()->toIso8601String(),
        ]);
        return false;
    }
}

/**
 * بث الحدث مع إعادة المحاولة عند الفشل
 */
function broadcastWithRetry($event, int $maxRetries = 2): bool
{
    $attempts = 0;
    $lastError = null;
    
    while ($attempts < $maxRetries) {
        $attempts++;
        
        try {
            if (canConnectToPusher()) {
                broadcast($event);
                Log::info('Pusher broadcast succeeded on attempt ' . $attempts, [
                    'event' => get_class($event),
                    'attempts' => $attempts,
                ]);
                return true;
            }
        } catch (\Exception $e) {
            $lastError = $e;
            Log::warning("Pusher broadcast attempt {$attempts} failed: " . $e->getMessage());
        }
        
        // انتظار قبل إعادة المحاولة
        if ($attempts < $maxRetries) {
            usleep(500000); // 0.5 ثانية
        }
    }
    
    Log::error("Pusher broadcast failed after {$maxRetries} attempts", [
        'event' => get_class($event),
        'last_error' => $lastError ? $lastError->getMessage() : 'Unknown error',
    ]);
    
    return false;
}

/**
 * بث الحدث مع تسجيل تفاصيل كاملة
 */
function broadcastWithDetails($event): bool
{
    $eventClass = get_class($event);
    $eventName = method_exists($event, 'broadcastAs') ? $event->broadcastAs() : $eventClass;
    
    $connectionStatus = canConnectToPusher();
    
    Log::info('Pusher broadcast attempt', [
        'event_class' => $eventClass,
        'event_name' => $eventName,
        'connection_status' => $connectionStatus ? 'connected' : 'disconnected',
        'timestamp' => now()->toIso8601String(),
    ]);
    
    $result = broadcastSafe($event);
    
    Log::info('Pusher broadcast result', [
        'event' => $eventName,
        'result' => $result ? 'success' : 'failed',
        'connection_status' => $connectionStatus ? 'connected' : 'disconnected',
        'timestamp' => now()->toIso8601String(),
    ]);
    
    return $result;
}