<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        // Statistiques de test pour l'utilisateur
        $stats = [
            'comptes' => 3,
            'transactions' => 24,
            'objectifs' => 2,
            'notifications_non_lues' => 5
        ];

        return view('profil.index', compact('stats'));
    }

    public function edit()
    {
        return view('profil.edit');
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);

        try {
            $user = Auth::user();
            $user->update($validatedData);

            return redirect()->route('profil.index')->with('success', 'Profil mis à jour avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour.'])->withInput();
        }
    }

    public function changePassword()
    {
        return view('profil.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validatedData['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        try {
            Auth::user()->update([
                'password' => Hash::make($validatedData['password'])
            ]);

            return redirect()->route('profil.index')->with('success', 'Mot de passe mis à jour avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour du mot de passe.']);
        }
    }
}
