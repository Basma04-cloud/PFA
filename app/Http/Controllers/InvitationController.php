<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationEnvoyee;

class InvitationController extends Controller
{
    public function index()
    {
        $invitationsEnvoyees = Invitation::where('expediteur_id', Auth::id())
            ->orderBy('date_envoi', 'desc')
            ->get();
            
        return view('invitations.index', compact('invitationsEnvoyees'));
    }

    public function create()
    {
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'destinataire_email' => 'required|email|max:255',
        ]);

        // Vérifier si l'utilisateur existe déjà
        $utilisateurExistant = Utilisateur::where('email', $request->destinataire_email)->first();
        if ($utilisateurExistant) {
            return redirect()->route('invitations.create')->with('error', 'Cet utilisateur est déjà inscrit sur la plateforme.');
        }

        // Vérifier si une invitation a déjà été envoyée à cet email
        $invitationExistante = Invitation::where('destinataire_email', $request->destinataire_email)
            ->where('statut', 'en attente')
            ->first();
            
        if ($invitationExistante) {
            return redirect()->route('invitations.create')->with('error', 'Une invitation a déjà été envoyée à cette adresse email.');
        }

        // Créer l'invitation
        $invitation = Invitation::create([
            'expediteur_id' => Auth::id(),
            'destinataire_email' => $request->destinataire_email,
            'statut' => 'en attente',
            'date_envoi' => now(),
        ]);

        // Envoyer l'email d'invitation
        try {
            Mail::to($request->destinataire_email)->send(new InvitationEnvoyee($invitation));
        } catch (\Exception $e) {
            return redirect()->route('invitations.index')->with('error', 'L\'invitation a été créée mais l\'email n\'a pas pu être envoyé: ' . $e->getMessage());
        }

        return redirect()->route('invitations.index')->with('success', 'Invitation envoyée avec succès!');
    }

    public function annuler(Invitation $invitation)
    {
        $this->authorize('update', $invitation);
        
        $invitation->statut = 'refusée';
        $invitation->save();

        return redirect()->route('invitations.index')->with('success', 'Invitation annulée avec succès!');
    }
}