<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use Illuminate\Http\Request;

class AspirationController extends Controller
{
    public function index(Request $request)
    {
        $query = Aspiration::latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        $aspirations = $query->paginate(15)->withQueryString();
        $unreadCount = Aspiration::where('is_read', false)->count();

        return view('aspirations.index', compact('aspirations', 'unreadCount'));
    }

    public function markAsRead(Aspiration $aspiration)
    {
        $aspiration->update(['is_read' => true]);

        return back()->with('success', 'Pesan ditandai sudah dibaca.');
    }

    public function destroy(Aspiration $aspiration)
    {
        $aspiration->delete();

        return back()->with('success', 'Pesan aspirasi berhasil dihapus.');
    }
}
