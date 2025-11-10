<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (!$user || !$user->role || $user->role->name !== 'Administrateur') {
                abort(403, 'Accès interdit');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $salles = Salle::orderBy('nom_salle')->paginate(10);
        return view('admin.salles.index', compact('salles'));
    }

    public function create()
    {
        return view('admin.salles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_salle' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'localisation' => 'required|string',
            'disponible' => 'nullable|boolean',
        ]);

        $validated['disponible'] = $request->boolean('disponible');
        Salle::create($validated);

        return redirect()->route('admin.salles.index')->with('status', 'Salle créée');
    }

    public function edit(Salle $salle)
    {
        return view('admin.salles.edit', compact('salle'));
    }

    public function update(Request $request, Salle $salle)
    {
        $validated = $request->validate([
            'nom_salle' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
            'localisation' => 'required|string',
            'disponible' => 'nullable|boolean',
        ]);

        $validated['disponible'] = $request->boolean('disponible');
        $salle->update($validated);
        return redirect()->route('admin.salles.index')->with('status', 'Salle mise à jour');
    }

    public function destroy(Salle $salle)
    {
        $salle->delete();
        return redirect()->route('admin.salles.index')->with('status', 'Salle supprimée');
    }

    public function toggle(Salle $salle)
    {
        $salle->update(['disponible' => !$salle->disponible]);
        return redirect()->route('admin.salles.index')->with('status', 'Disponibilité mise à jour');
    }
}