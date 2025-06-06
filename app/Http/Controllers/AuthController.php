<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function connexionpage()
    {
        return view('home');
    }

    public function inscription()
    {
        return view('register');
    }

    public function connexion(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

         
        if (Auth::attempt($credentials)) {
    $request->session()->regenerate();

    $user = Auth::user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('dashboard');
    }
}


        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->except('password'));
    }

    public function enregistrer(Request $request)
    {
        

        // Validation des données
        
        $validatedData = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
    'role' => ['required', 'in:user,admin'],  
        ]);

        try {
            // Création de l'utilisateur
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => $validatedData['role']
            ]);

            // Connexion automatique de l'utilisateur
            Auth::login($user);

            // Redirection vers le dashboard avec un message de succès
            
     // Redirection selon le rôle
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Bienvenue Admin ' . $user->name);
}       else {
            return redirect()->route('dashboard')->with('success', 'Bienvenue ' . $user->name);
        }


        } catch (\Exception $e) {
            // En cas d'erreur, retourner avec un message d'erreur
            return back()->withErrors([
                'email' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }
        dd(Auth::user()->role);

    }

    public function deconnexion(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
