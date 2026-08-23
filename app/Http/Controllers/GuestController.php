<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Agenda;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        // Menampilkan daftar tamu terbaru beserta nama agendanya
        $guests = Guest::with('agenda')->latest()->get();
        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        $agendas = Agenda::orderBy('date', 'desc')->get();
        return view('guests.create', compact('agendas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
        ]);

        Guest::create($request->all());

        return redirect()->route('guests.index')->with('success', 'Data tamu berhasil ditambahkan.');
    }

    public function edit(Guest $guest)
    {
        $agendas = Agenda::orderBy('date', 'desc')->get();
        return view('guests.edit', compact('guest', 'agendas'));
    }

    public function update(Request $request, Guest $guest)
    {
        $request->validate([
            'agenda_id' => 'required|exists:agendas,id',
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
        ]);

        $guest->update($request->all());

        return redirect()->route('guests.index')->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect()->route('guests.index')->with('success', 'Data tamu berhasil dihapus.');
    }
}
