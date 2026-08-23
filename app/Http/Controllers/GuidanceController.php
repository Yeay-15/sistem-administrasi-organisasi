<?php

namespace App\Http\Controllers;

use App\Models\Guidance;
use App\Models\Member;
use Illuminate\Http\Request;

class GuidanceController extends Controller
{
    public function index()
    {
        // Mengambil data pembinaan beserta relasi pengurus dan divisi pengurus tersebut
        $guidances = Guidance::with(['member.division'])->latest('date')->get();
        return view('guidances.index', compact('guidances'));
    }

    public function create()
    {
        // Hanya menampilkan pengurus yang masih ada (tidak di-soft delete)
        $members = Member::orderBy('name', 'asc')->get();
        return view('guidances.create', compact('members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'date' => ['required', 'date'],
            'type' => ['required', 'in:Teguran Lisan,Teguran Tertulis,SP 1,SP 2,SP 3,Lainnya'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'counselor' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Berlaku,Selesai,Dicabut'],
        ]);

        Guidance::create($request->all());

        return redirect()->route('guidances.index')->with('success', 'Data pembinaan berhasil dicatat.');
    }

    public function edit(Guidance $guidance)
    {
        $members = Member::orderBy('name', 'asc')->get();
        return view('guidances.edit', compact('guidance', 'members'));
    }

    public function update(Request $request, Guidance $guidance)
    {
        $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'date' => ['required', 'date'],
            'type' => ['required', 'in:Teguran Lisan,Teguran Tertulis,SP 1,SP 2,SP 3,Lainnya'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'counselor' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Berlaku,Selesai,Dicabut'],
        ]);

        $guidance->update($request->all());

        return redirect()->route('guidances.index')->with('success', 'Data pembinaan berhasil diperbarui.');
    }

    public function destroy(Guidance $guidance)
    {
        $guidance->delete();
        return redirect()->route('guidances.index')->with('success', 'Data pembinaan berhasil dihapus.');
    }
}
