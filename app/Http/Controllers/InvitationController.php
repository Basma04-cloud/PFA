<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationEnvoyee;
use Illuminate\Support\Str;


class InvitationController extends Controller
{
    
public function index()
{
    $invitations = \App\Models\Invitation::latest()->paginate(10);
    return view('admin.invitations.index', compact('invitations'));
}


public function send(Request $request)
{
    $request->validate(['email' => 'required|email|unique:users,email|unique:invitations,email']);

    $invitation = Invitation::create([
        'email' => $request->email,
        'token' => Str::uuid(),
        'invited_by' => auth()->id(),
        'expires_at' => now()->addDays(7),
    ]);

    Mail::to($request->email)->send(new \App\Mail\InvitationMail($invitation));

    return redirect()->back()->with('success', 'Invitation envoyée avec succès !');
}

}