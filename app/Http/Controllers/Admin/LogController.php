<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', '');

        $logs = ActivityLog::query()
            ->when($q !== '', function ($qry) use ($q): void {
                $qry->where(function ($w) use ($q): void {
                    $w->where('user_name', 'like', "%{$q}%")
                        ->orWhere('path', 'like', "%{$q}%")
                        ->orWhere('route_name', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn ($qry) => $qry->where('status', $status))
            ->latest('id')
            ->limit(500)
            ->get();

        return view('admin.log.index', ['logs' => $logs, 'q' => $q, 'status' => $status]);
    }
}
