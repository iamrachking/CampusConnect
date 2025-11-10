<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use Illuminate\Http\Request;

class MaterielController extends Controller
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
        $materiels = Materiel::orderBy('nom_materiel')->paginate(10);
        return view('admin.materiels.index', compact('materiels'));
    }

    public function create()
    {
        return view('admin.materiels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_materiel' => 'required|string|max:255',
            'disponible' => 'nullable|boolean',
        ]);

        $validated['disponible'] = $request->boolean('disponible');
        Materiel::create($validated);

        return redirect()->route('admin.materiels.index')->with('status', 'Matériel créé');
    }

    public function edit(Materiel $materiel)
    {
        return view('admin.materiels.edit', compact('materiel'));
    }

    public function update(Request $request, Materiel $materiel)
    {
        $validated = $request->validate([
            'nom_materiel' => 'required|string|max:255',
            'disponible' => 'nullable|boolean',
        ]);

        $validated['disponible'] = $request->boolean('disponible');
        $materiel->update($validated);
        return redirect()->route('admin.materiels.index')->with('status', 'Matériel mis à jour');
    }

    public function destroy(Materiel $materiel)
    {
        $materiel->delete();
        return redirect()->route('admin.materiels.index')->with('status', 'Matériel supprimé');
    }

    public function toggle(Materiel $materiel)
    {
        $materiel->update(['disponible' => !$materiel->disponible]);
        return redirect()->route('admin.materiels.index')->with('status', 'Disponibilité mise à jour');
    }
}