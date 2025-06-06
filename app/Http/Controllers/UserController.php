<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{ 

public function show($id){
    $user = User::with(['transactions', 'comptes', 'objectifs'])->findOrFail($id);
return view('admin.users.show', ['user' => $user, 
'totalUsers' => User::count(),
'totalRevenus' => $user->transactions->where('montant', '>', 0)->sum('montant'),
'totalDepenses' => $user->transactions->where('montant', '<', 0)->sum('montant'), ]);
}
public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('admin.users')->with('success', 'Utilisateur supprimé avec succès.');
}


}


