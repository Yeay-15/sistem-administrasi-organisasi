@extends('layouts.app')

@section('title', 'Buat Surat dari Template - KATIBER')

@php
    $selectClass = 'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white';
    $cardClass = 'theme-transition group flex flex-col rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-500/50';
    $committeeTemplates = $templates->filter(fn ($t) => $t->isCommitteeBased());
    $orgTemplates = $templates->reject(fn ($t) => $t->isCommitteeBased());
@endphp

@section('content')
    <div class="mx-auto max-w-5xl"
        x-data="{
            hub: @js($hub),
            picked: '',
            warn: false,
            get current() { return this.hub.committees.find(c => String(c.id) === String(this.picked)) || null; },
            get missing() {
                const c = this.current;
                if (!c) return [];
                const m = [];
                if (!c.acara) m.push('tanggal acara');
                if (!c.theme) m.push('tema');
                if (!c.time) m.push('jam');
                if (!c.venue) m.push('tempat');
                return m;
            },
            link(code) { return this.picked ? this.hub.urls[code].replace('__C__', this.picked) : '#'; },
            go(e) { if (!this.picked) { e.preventDefault(); this.warn = true; this.$refs.sel.focus(); } },
        }">

        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('outgoing-letters.index') }}"
                class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Buat Surat dari Template</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Hasilnya file Word atau PDF, dan otomatis tercatat di Surat Keluar sebagai Draft.</p>
            </div>
        </div>

        @if ($committeeTemplates->isNotEmpty())
            <section class="mb-10">
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Surat untuk Acara / Kepanitiaan</h2>

                <div class="theme-transition mb-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">1. Pilih kepanitiaan</label>
                    <select x-ref="sel" x-model="picked" @change="warn = false" class="{{ $selectClass }}">
                        <option value="">-- Pilih kepanitiaan --</option>
                        <template x-for="c in hub.committees" :key="c.id">
                            <option :value="c.id" x-text="c.name + (c.status === 'Selesai' ? ' (Selesai)' : '')"></option>
                        </template>
                    </select>
                    <p x-show="warn && !picked" x-cloak class="mt-1.5 text-sm text-red-500 dark:text-red-400">Pilih kepanitiaannya dulu.</p>
                    <p x-show="!hub.committees.length" x-cloak class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                        Belum ada kepanitiaan. <a href="{{ route('committees.index') }}" class="font-semibold text-blue-600 underline dark:text-blue-400">Buat kepanitiaan dulu</a>.
                    </p>

                    <div x-show="current" x-cloak class="mt-3 rounded-lg bg-slate-50 px-3.5 py-3 text-sm text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                        <p><span class="font-semibold">Acara:</span> <span x-text="current && current.acara ? current.acara : '(belum diisi)'"></span></p>
                        <p><span class="font-semibold">Tema:</span> <span x-text="current && current.theme ? current.theme : '(belum diisi)'"></span></p>
                        <p><span class="font-semibold">Jam:</span> <span x-text="current && current.time ? current.time : '(belum diisi)'"></span>
                            &middot; <span class="font-semibold">Tempat:</span> <span x-text="current && current.venue ? current.venue : '(belum diisi)'"></span></p>
                        <p x-show="missing.length" class="mt-2 text-amber-700 dark:text-amber-300">
                            Belum lengkap: <span x-text="missing.join(', ')"></span>. Boleh diketik manual di form surat, atau lengkapi dulu di
                            <a :href="'{{ url('/kepanitiaan') }}/' + picked + '/ubah'" class="font-semibold underline">data kepanitiaan</a>.
                        </p>
                    </div>
                </div>

                <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">2. Pilih jenis surat</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($committeeTemplates as $t)
                        <a :href="link('{{ $t->code }}')" @click="go($event)" :class="picked ? '' : 'opacity-60'" class="{{ $cardClass }}">
                            @include('letter_generator._card_body', ['t' => $t])
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($orgTemplates->isNotEmpty())
            <section>
                <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Surat Organisasi (tanpa kepanitiaan)</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($orgTemplates as $t)
                        <a href="{{ route('letter-generator.create', $t) }}" class="{{ $cardClass }}">
                            @include('letter_generator._card_body', ['t' => $t])
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($templates->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                Belum ada template. Jalankan <code>php artisan db:seed --class=LetterTemplateSeeder</code>.
            </div>
        @endif
    </div>
@endsection
