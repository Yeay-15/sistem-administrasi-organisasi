<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KATIBER</title>

    <script>
        (function() {
            var stored = localStorage.getItem('katiber-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950 px-4 theme-transition">

    <div class="w-full max-w-md">
        <div class="mb-8 flex flex-col items-center text-center">
            <div
                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-xl font-bold text-white shadow-lg shadow-blue-600/20">
                K</div>
            <h1 class="mt-4 text-xl font-bold text-slate-800 dark:text-white">Sistem KATIBER</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Panel Administrasi</p>
        </div>

        <div
            class="theme-transition rounded-2xl border border-slate-100 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="mb-6 text-lg font-bold text-slate-800 dark:text-white">Login Administrator</h2>

            @if ($errors->any())
                <div
                    class="mb-5 flex items-start gap-2.5 rounded-lg border border-red-200 bg-red-50 px-3.5 py-3 text-sm text-red-700 dark:border-red-800/60 dark:bg-red-500/10 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75"
                        stroke="currentColor" class="mt-0.5 h-4.5 w-4.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                </div>
                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]">
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-blue-600 py-2.5 px-4 font-semibold text-white transition hover:bg-blue-700">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>

</html>
