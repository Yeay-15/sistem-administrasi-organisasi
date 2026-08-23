<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::all();
        return view('divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('divisions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:50'],
        ]);

        Division::create($request->all());

        return redirect()->route('divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Division $division)
    {
        return view('divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:50'],
        ]);

        $division->update($request->all());

        return redirect()->route('divisions.index')->with('success', 'Data divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        try {
            $division->delete();
            return redirect()->route('divisions.index')->with('success', 'Divisi berhasil dihapus.');
        } catch (QueryException $e) {
            // Menangkap error jika divisi masih memiliki pengurus yang terhubung (restrictOnDelete)
            return redirect()->route('divisions.index')->with('error', 'Divisi tidak dapat dihapus karena masih memiliki data pengurus.');
        }
    }
}
