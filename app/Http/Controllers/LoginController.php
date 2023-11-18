<?php

namespace App\Http\Controllers;

use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RedirectIfNotAuthenticated;
use Illuminate\Routing\Middleware;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('login.index',['title'=>'Login','active'=>'login']);
    }

    public function __construct()
    {
        $this->middleware('auth.redirect')->except(['index', 'authenticate']);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function authenticate(Request $request)
    {
        $this->middleware('auth.redirect');
        
        $validate = $request->validate(
            [
                'email' => 'required',
                'password'=>'required'
            ]
        );

        // cek jika sudah berhasil login
        if (Auth::attempt($validate)) {
            $request->session()->regenerate();
            //direct ke halaman admin
            return redirect('/');
        }
        //jika gagal login
        return back()->with('loginError','Login gagal, Periksa kembali email dan Password');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
