<?php

namespace App\Http\Controllers;

use App\Models\CommitteeField;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommitteeFieldController extends Controller
{
    public function index()
    {
        $fields = CommitteeField::withCount('committeeMembers')->orderBy('sort_order')->orderBy('name')->get();

        return view('committee_fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('committee_fields', 'name')],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        // Urutan kosong -> pakai urutan bawaan sesuai nama (Acara, Perlengkapan, ...), selain itu 100.
        CommitteeField::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'sort_order' => $request->filled('sort_order') ? (int) $request->input('sort_order') : CommitteeField::presetOrder($request->input('name')),
        ]);

        return redirect()->route('committee-fields.index')->with('success', 'Bidang panitia berhasil ditambahkan.');
    }

    public function update(Request $request, CommitteeField $committee_field)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('committee_fields', 'name')->ignore($committee_field->id)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $committee_field->update(array_filter([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'sort_order' => $request->filled('sort_order') ? (int) $request->input('sort_order') : null,
        ], fn ($v, $k) => $k === 'description' || $v !== null, ARRAY_FILTER_USE_BOTH));

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
