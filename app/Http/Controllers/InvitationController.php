<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\Equipe;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with('projet', 'expediteur')
                        ->where('destinataire_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('invitations.index', compact('invitations'));
    }

     public function respond(Request $request, Invitation $invitation)
    {
        if ($invitation->destinataire_id !== Auth::id()) {
            abort(403, 'Action non autorisée');
        }

        $request->validate([
            'action' => 'required|in:accept,decline',
        ]);

        if ($request->action === 'accept') {
            Equipe::create([
                'projet_id' => $invitation->projet_id,
                'user_id' => Auth::id(),
                'role_membre' => 'membre', 
            ]);

            $invitation->statut = 'accepted';
        } else {
            $invitation->statut = 'declined';
        }

        $invitation->is_read = true;
        $invitation->save();

        return redirect()->back()->with('success', 'Votre réponse a été enregistrée !');
    }
}
