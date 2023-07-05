<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Log;

class LogUserAction
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            $log = new Log();
            $log->user_id = $user->id;
            $log->endpoint = $request->fullUrl();

            if ($request->isMethod('post')) {
                $log->action = 'create';
            } elseif ($request->isMethod('put') || $request->isMethod('patch')) {
                $log->action = 'edit';
            } elseif ($request->isMethod('delete')) {
                $log->action = 'delete';
            }

            $log->save();
        }

        return $next($request);
    }
}