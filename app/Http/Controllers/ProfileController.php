<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Halaman "Pengaturan Akun" milik pengguna sendiri. Ini murni ranah privat
     * (avatar kasual, nama tampilan, sandi) — TERPISAH dari Data Pengurus
     * (foto resmi PDH, jabatan) yang hanya bisa diubah Sekretariat lewat
     * menu Kelola Pengurus.
     */
    public function edit()
    {
        $user = Auth::user()->load('member.division');

        return view('profile.edit', compact('user'));
    }

    /**
     * Update data akun: nama tampilan & avatar.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $user->name = $request->name;

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user);
            $filename = Str::uuid() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $user->avatar_path = $request->file('avatar')->storeAs('avatars', $filename, 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($user);
            $user->avatar_path = null;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ubah sandi mandiri (wajib memasukkan sandi lama).
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Sandi lama yang Anda masukkan salah.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        AuditLog::record('Ubah Sandi', 'Mengubah sandi akun sendiri.');

        return back()->with('success', 'Sandi berhasil diperbarui.');
    }

    private function deleteAvatar($user): void
    {
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }
    }
}
