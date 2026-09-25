<?php

namespace App\Http\Controllers;

use App\Models\PanitiaBidang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PanitiaBidangController extends Controller
{
    public function index()
    {
        $bidangList = PanitiaBidang::withCount('committeeMembers')->orderBy('name')->get();

        return view('panitia_bidang.index', compact('bidangList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('panitia_bidang', 'name')],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        PanitiaBidang::create($request->only(['name', 'description']));

        return redirect()->route('bidang-panitia.index')->with('success', 'Bidang panitia berhasil ditambahkan.');
    }

    public function update(Request $request, PanitiaBidang $bidang_panitia)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('panitia_bidang', 'name')->ignore($bidang_panitia->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $bidang_panitia->update($request->only(['name', 'description']));

        return redirect()->route('bidang-panitia.index')->with('success', 'Bidang panitia berhasil diperbarui.');
    }

    public function destroy(PanitiaBidang $bidang_panitia)
    {
        if ($bidang_panitia->committeeMembers()->exists()) {
            return redirect()->route('bidang-panitia.index')
                ->with('error', 'Bidang ini masih dipakai di sebuah kepanitiaan, tidak bisa dihapus.');
        }

        $bidang_panitia->delete();

        return redirect()->route('bidang-panitia.index')->with('success', 'Bidang panitia berhasil dihapus.');
    }
}
