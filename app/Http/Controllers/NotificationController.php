<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Notification::where('user_id', Auth::id());
            
            // Utiliser le bon champ de date selon la structure
            $dateField = Schema::hasColumn('notifications', 'date_envoi') ? 'date_envoi' : 'created_at';
            $query->orderBy($dateField, 'desc');

            // Filtres (seulement si les colonnes existent)
            if ($request->filled('type') && Schema::hasColumn('notifications', 'type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('statut')) {
                if ($request->statut === 'non_lu') {
                    $query->nonLues();
                } elseif ($request->statut === 'lu') {
                    $query->lues();
                }
            }

            $notifications = $query->paginate(20);

            // Statistiques
            $stats = [
                'total' => Notification::where('user_id', Auth::id())->count(),
                'non_lues' => Notification::where('user_id', Auth::id())->nonLues()->count(),
                'cette_semaine' => Notification::where('user_id', Auth::id())
                    ->where($dateField, '>=', now()->subWeek())->count()
            ];

            return view('notifications.index', compact('notifications', 'stats'));

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des notifications', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return view('notifications.index', [
                'notifications' => collect(),
                'stats' => ['total' => 0, 'non_lues' => 0, 'cette_semaine' => 0],
                'error' => 'Erreur lors du chargement des notifications'
            ]);
        }
    }

    public function marquerCommeLu($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                                      ->where('id', $id)
                                      ->firstOrFail();

            $notification->marquerCommeLu();

            return redirect()->route('notifications.index')
                           ->with('success', 'Notification marquée comme lue');

        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de la notification', [
                'error' => $e->getMessage(),
                'notification_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('notifications.index')
                           ->with('error', 'Erreur lors du marquage de la notification');
        }
    }

    public function marquerToutCommeLu()
    {
        try {
            $updateData = ['lu' => true];
            if (Schema::hasColumn('notifications', 'lu_at')) {
                $updateData['lu_at'] = now();
            }

            $count = Notification::where('user_id', Auth::id())
                                ->nonLues()
                                ->update($updateData);

            return redirect()->route('notifications.index')
                           ->with('success', "{$count} notification(s) marquée(s) comme lue(s)");

        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de toutes les notifications', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('notifications.index')
                           ->with('error', 'Erreur lors du marquage des notifications');
        }
    }

    public function destroy($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                                      ->where('id', $id)
                                      ->firstOrFail();

            $notification->delete();

            return redirect()->route('notifications.index')
                           ->with('success', 'Notification supprimée');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la notification', [
                'error' => $e->getMessage(),
                'notification_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('notifications.index')
                           ->with('error', 'Erreur lors de la suppression');
        }
    }

    public function supprimerLues()
    {
        try {
            $count = Notification::where('user_id', Auth::id())
                                ->lues()
                                ->delete();

            return redirect()->route('notifications.index')
                           ->with('success', "{$count} notification(s) lue(s) supprimée(s)");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression des notifications lues', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('notifications.index')
                           ->with('error', 'Erreur lors de la suppression');
        }
    }

    public function creerTest()
    {
        try {
            Notification::creerNotification(
                Auth::id(),
                'Ceci est une notification de test créée le ' . now()->format('d/m/Y à H:i'),
                'Notification de test',
                'info'
            );

            return redirect()->route('notifications.index')
                           ->with('success', 'Notification de test créée');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la notification de test', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('notifications.index')
                           ->with('error', 'Erreur lors de la création de la notification de test');
        }
    }

    public function getNonLues()
    {
        try {
            $count = Notification::where('user_id', Auth::id())->nonLues()->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur'], 500);
        }
    }
}

