<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login');
        }

        if (!$this->checkrole($user, $role)) {
            $dashboardRoute = $this->getDashboardRoute($user->role);
            return redirect()->route($dashboardRoute);
        }

        return $next($request);
    }

    public function checkrole($user, $role)
    {
        return $user->role === $role;
    }

    public function getDashboardRoute($role)
    {
        switch ($role) {
            case 'college':
                return 'college.dashboard';
                break;
            case 'campus_records':
                return 'campus_records.dashboard';
                break;
            case 'campus_extensions':
                return 'campus_extension.dashboard';
                break;
            case 'chancellor':
                return 'chancellor.dashboard';
                break;
            case 'admin':
                return 'system.dashboard';
                break;
            // add more cases for other roles as needed
            default:
                return 'login';
        }
    }
}
