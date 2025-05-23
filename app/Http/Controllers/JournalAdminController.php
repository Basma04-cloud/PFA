<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalAdmin;
use Illuminate\Support\Facades\Auth;

class JournalAdminController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', JournalAdmin::class);
        
        $journaux = JournalAdmin::with('utilisateur')
            ->orderBy('date_action', 'desc')
            ->paginate(50);
            
        return view('journal-admin.index', compact('journaux'));
    }

    public static function logAction($action, $typeAction)
    {
        return JournalAdmin::logAction(Auth::id(), $action, $typeAction);
    }
}