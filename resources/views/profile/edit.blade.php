@extends('layouts.app')

@section('title', 'Pengaturan Akun - KATIBER')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pengaturan Akun</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola avatar, nama tampilan, dan sandi akun Anda.</p>
        </div>

        @if (session('success'))
            <div class="theme-transition rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800/60 dark:bg-green-500/10 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        {{-- ============ INFORMASI AKUN (Avatar & Nama) ============ --}}
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Informasi Akun</h2>
            <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Avatar ini bersifat pribadi — hanya tampil di panel admin, tidak dipakai di portal publik.</p>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-5"
                x-data="{ preview: @js($user->avatar_url), removed: false }">
                @csrf
                @method('PATCH')

                <div class="flex items-center gap-5">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                        <template x-if="preview && !removed">
                            <img :src="preview" alt="Avatar" class="h-full w-full object-cover">
                        </template>
                        <template x-if="!preview || removed">
                            <span class="text-xl font-bold text-slate-400 dark:text-slate-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </template>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="avatar" accept="image/png, image/jpeg, image/webp"
                            @change="if ($event.target.files.length) { preview = URL.createObjectURL($event.target.files[0]); removed = false }"
                            class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-400">
                        <p class="mt-1 text-xs text-slate-400">Format JPG/PNG/WEBP, maksimal 2MB.</p>
                        @error('avatar')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        @if ($user->avatar_url)
                            <label class="mt-2 inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                <input type="checkbox" name="remove_avatar" value="1" x-model="removed"
                                    class="rounded border-slate-300 text-red-600 focus:ring-red-500 dark:border-slate-600 dark:bg-slate-800">
                                Hapus avatar saat ini
                            </label>
                        @endif
                    </div>
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Tampilan</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full max-w-sm rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Email Login</label>
                    <input type="text" value="{{ $user->email }}" disabled
                        class="w-full max-w-sm rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-400 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-500">
                    <p class="mt-1 text-xs text-slate-400">Hubungi Super Admin jika perlu mengganti email login.</p>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:shadow-blue-600/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ============ UBAH SANDI ============ --}}
        <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <h2 class="text-base font-semibold text-slate-800 dark:text-white">Ubah Sandi</h2>
            <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">Masukkan sandi lama untuk konfirmasi sebelum menyimpan sandi baru.</p>

            <form action="{{ route('profile.update-password') }}" method="POST" class="mt-5 max-w-sm space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Sandi Saat Ini</label>
                    <input type="password" name="current_password" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    @error('current_password')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Sandi Baru</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <p class="mt-1 text-xs text-slate-400">Minimal 8 karakter.</p>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Konfirmasi Sandi Baru</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                        Ubah Sandi
                    </button>
                </div>
            </form>
        </div>

        {{-- ============ DATA KEPENGURUSAN (READ-ONLY) ============ --}}
        @if ($user->member)
            <div class="theme-transition rounded-2xl border border-slate-100 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
                <h2 class="text-base font-semibold text-slate-800 dark:text-white">Data Kepengurusan</h2>
                <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                    Ini adalah data resmi organisasi (tampil di portal publik) — dikelola oleh Sekretariat lewat menu Kelola Pengurus, bukan dari sini.
                </p>

                <div class="mt-5 flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        @if ($user->member->photo_url)
                            <img src="{{ $user->member->photo_url }}" alt="{{ $user->member->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-lg font-bold text-slate-400 dark:text-slate-600">{{ strtoupper(substr($user->member->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">{{ $user->member->name }}</p>
                        <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                            {{ $user->member->position }} &middot; {{ $user->member->division->name ?? '-' }}
                        </p>
                    </div>
                    @can('manage_members')
                        <a href="{{ route('members.edit', $user->member->id) }}"
                            class="ml-auto shrink-0 rounded-lg border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                            Edit Data Pengurus
                        </a>
                    @endcan
                </div>
            </div>
        @endif
    </div>
@endsection
