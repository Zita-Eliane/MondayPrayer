<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class LeaderController extends Controller
{
    public function index()
    {
        $leaders = Person::orderBy('name')->get();
        return view('leaders.index', compact('leaders'));
    }

    public function create()
    {
        return view('leaders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Person::create([
            'name' => $data['name'],
            'type' => 'leader',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('leaders.index')->with('success', 'Dirigeant ajouté ✅');
    }

    public function edit(Person $leader)
    {
        return view('leaders.edit', compact('leader'));
    }

    public function update(Request $request, Person $leader)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $leader->update([
            'name' => $data['name'],
        ]);

        return redirect()->route('leaders.index')->with('success', 'Dirigeant mis à jour ✅');
    }

    public function destroy(Person $leader)
    {
        $leader->delete();
        return redirect()->route('leaders.index')->with('success', 'Dirigeant supprimé 🗑️');
    }
}
