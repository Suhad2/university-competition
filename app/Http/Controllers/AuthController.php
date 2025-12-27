<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
   if (Auth::check()) {
    return $this->redirectToDashboard();
}
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
    $request->session()->regenerate();

    return $this->redirectToDashboard();
}

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegister()
    {
if (Auth::check()) {
    return $this->redirectToDashboard();
}
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'university' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'university' => $request->university,
            'role' => 'user',
        ]);
Auth::login($user);

return $this->redirectToDashboard();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }

private function redirectToDashboard()
{
    $user = Auth::user();

    return match ($user->role) {
        'admin'         => redirect()->route('admin.dashboard'),
        'exam_manager'  => redirect()->route('exam-manager.dashboard'),
        default         => redirect()->route('dashboard'),
    };
}
}