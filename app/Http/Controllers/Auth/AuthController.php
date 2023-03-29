<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid email or password']);
        }

        if (!Auth::attempt($credentials)) {
            return redirect()->route('login')->withErrors(['password' => 'Incorrect password']);
        }
        $user = Auth::user();
        $role = $user->role;
        
        switch ($role) {
            case 'college':
                return redirect()->route('college.dashboard');
                break;
            case 'campus_records':
                return redirect()->route('campus_records.dashboard');
                break;
            case 'campus_extensions':
                return redirect()->route('campus_extension.dashboard');
                break;
            case 'chancellor':
                return redirect()->route('chancellor.dashboard');
                break;
            case 'admin':
                return redirect()->route('system.dashboard');
                break;
            // add more cases for other roles as needed
            default:
                return redirect()->route('login');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

        

}
