<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('date_envoi', 'desc')
            ->paginate(20);
            
        return view('notifications.index', compact('notifications'));
    }

    public function marquerCommeLu(Notification $notification)
    {
        $this->authorize('update', $notification);
        
        $notification->marquerCommeLu();

        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    public function marquerToutCommeLu()
    {
        Notification::where('user_id', Auth::id())
            ->where('lu', false)
            ->update(['lu' => true]);

        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        
        $notification->delete();

        return redirect()->route('notifications.index')->with('success', 'Notification supprimée avec succès!');
    }
}