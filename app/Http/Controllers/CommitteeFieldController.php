<?php

namespace App\Http\Controllers;

use App\Models\CommitteeField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommitteeFieldController extends Controller
{
    public function index()
    {
        $fields = CommitteeField::withCount('committeeMembers')->orderBy('name')->get();

        return view('committee_fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('committee_fields', 'name')],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        CommitteeField::create($request->only(['name', 'description']));

        return redirect()->route('committee-fields.index')->with('success', 'Bidang panitia berhasil ditambahkan.');
    }

    public function update(Request $request, CommitteeField $committee_field)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('committee_fields', 'name')->ignore($committee_field->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $committee_field->update($request->only(['name', 'description']));

        return redirect()->route('committee-fields.index')->with('success', 'Bidang panitia berhasil diperbarui.');
    }

    public function destroy(CommitteeField $committee_field)
    {
        if ($committee_field->committeeMembers()->exists()) {
            return redirect()->route('committee-fields.index')
                ->with('error', 'Bidang ini masih dipakai di sebuah kepanitiaan, tidak bisa dihapus.');
        }

        $committee_field->delete();

        return redirect()->route('committee-fields.index')->with('success', 'Bidang panitia berhasil dihapus.');
    }
}
