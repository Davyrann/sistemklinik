<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Audit trail: logs every write request (non-GET) plus any request
 * returning >= 400, including unhandled exceptions (re-thrown after logging).
 * Skip: logged-in GET reads (large volume, low audit value).
 */
class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        // Logout wipes the session before we log, so snapshot the user when the event fires.
        Event::listen(Logout::class, function (Logout $event) use ($request): void {
            $request->attributes->set('activity_log_user', $event->user);
        });

        try {
            $response = $next($request);
        } catch (Throwable $e) {
            $status = $e instanceof HttpExceptionInterface
                ? $e->getStatusCode()
                : 500;
            $this->log($request, $status, $e::class.': '.$e->getMessage());

            throw $e;
        }

        $isAction = $request->method() !== 'GET';
        $isError = $response->getStatusCode() >= 400;

        if ($isAction || $isError) {
            $this->log($request, $response->getStatusCode());
        }

        return $response;
    }

    private function log(Request $request, int $status, ?string $error = null): void
    {
        $user = $request->user() ?? $request->attributes->get('activity_log_user');

        ActivityLog::create([
            // User may have just deleted their own account — FK would fail.
            'user_id' => $user?->exists ? $user->id : null,
            'user_name' => $user?->name,
            'role' => $user?->role,
            'method' => $request->method(),
            'path' => '/'.$request->path(),
            'route_name' => $request->route()?->getName(),
            'status' => $status,
            'error' => $error,
            'ip' => $request->ip(),
        ]);
    }
}
