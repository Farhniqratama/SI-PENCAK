<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Userpt;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;

        $user = (new User())->where('username', $username)->first();
        $userpt = (new Userpt())->where('username', $username)->first();

        // In CI4: password_verify($password, $user['password'])
        // Using password_verify to check compatibility with existing database hashes
        if ($user && password_verify($password, $user->password)) {
            Auth::guard('web')->login($user);
            session()->put('role', 'operator');
            session()->put('user_id', $user->id);
            session()->put('username', $user->username);
            session()->put('nama', $user->nama ?? 'Operator');
            return redirect()->intended('/dashboard');
        } elseif ($userpt && password_verify($password, $userpt->password) && $userpt->status == 'aktif') {
            Auth::guard('admin')->login($userpt);
            session()->put('role', 'admin');
            session()->put('user_id', $userpt->id);
            session()->put('username', $userpt->username);
            session()->put('nama', $userpt->penanggung_jawab ?? $userpt->username);
            session()->put('pt', $userpt->id_pt);
            return redirect()->intended('/home');
        }

        return redirect('/login')->with('error', 'Username atau Password salah.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
