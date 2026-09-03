<?php

namespace App\Http\Controllers;

use App\Models\HomeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = HomeSetting::current();

        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomeSetting::current();

        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'mimes:png,webp,jpg,jpeg,svg', 'max:1024'],
            'remove_logo' => ['nullable', 'boolean'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'chairman_name' => ['nullable', 'string', 'max:255'],
            'chairman_message' => ['nullable', 'string', 'max:2000'],
            'chairman_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_chairman_photo' => ['nullable', 'boolean'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'aspiration_mode' => ['required', 'in:public,pengurus_only,nonaktif'],
        ]);

        $data = [
            'hero_title' => $validated['hero_title'] ?? null,
            'hero_subtitle' => $validated['hero_subtitle'] ?? null,
            'chairman_name' => $validated['chairman_name'] ?? null,
            'chairman_message' => $validated['chairman_message'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'aspiration_mode' => $validated['aspiration_mode'],
        ];

        if ($request->hasFile('logo')) {
            $this->deleteFile($setting->logo_path);
            $data['logo_path'] = $this->storeImage($request, 'logo', 'settings');
        } elseif ($request->boolean('remove_logo')) {
            $this->deleteFile($setting->logo_path);
            $data['logo_path'] = null;
        }

        if ($request->hasFile('hero_image')) {
            $this->deleteFile($setting->hero_image_path);
            $data['hero_image_path'] = $this->storeImage($request, 'hero_image', 'settings');
        } elseif ($request->boolean('remove_hero_image')) {
            $this->deleteFile($setting->hero_image_path);
            $data['hero_image_path'] = null;
        }

        if ($request->hasFile('chairman_photo')) {
            $this->deleteFile($setting->chairman_photo_path);
            $data['chairman_photo_path'] = $this->storeImage($request, 'chairman_photo', 'settings');
        } elseif ($request->boolean('remove_chairman_photo')) {
            $this->deleteFile($setting->chairman_photo_path);
            $data['chairman_photo_path'] = null;
        }

        $setting->update($data);

        return redirect()->route('settings.edit')->with('success', 'Pengaturan Beranda berhasil diperbarui.');
    }

    private function storeImage(Request $request, string $field, string $folder): string
    {
        $filename = Str::uuid() . '.' . $request->file($field)->getClientOriginalExtension();
        return $request->file($field)->storeAs($folder, $filename, 'public');
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
