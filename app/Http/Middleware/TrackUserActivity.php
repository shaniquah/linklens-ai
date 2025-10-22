<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && $request->isMethod('GET') && !$request->ajax()) {
            $user = Auth::user();
            $route = $request->route()->getName();
            
            // Track specific route visits
            $trackableRoutes = [
                'dashboard' => 'Visited main dashboard',
                'linkedin.dashboard' => 'Accessed LinkedIn automation',
                'settings.profile' => 'Viewed profile settings',
            ];

            if (isset($trackableRoutes[$route])) {
                $user->logActivity('page_visit', $trackableRoutes[$route], [
                    'route' => $route,
                    'url' => $request->url(),
                ]);
            }
        }

        return $response;
    }
}