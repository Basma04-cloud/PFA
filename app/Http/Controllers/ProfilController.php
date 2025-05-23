<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profil.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'f_name' => 'nullable|string|max:255',
            'l_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:utilisateur,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
        ];

        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il ne s'agit pas de l'avatar par défaut
            if ($user->avatar !== 'default-user-image.png') {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }
            
            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->storeAs('avatars', $avatarName, 'public');
            $userData['avatar'] = $avatarName;
        }

        $user->update($userData);

        return redirect()->route('profil.index')->with('success', 'Profil mis à jour avec succès!');
    }

    public function changePassword()
    {
        return view('profil.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profil.index')->with('success', 'Mot de passe mis à jour avec succès!');
    }
}