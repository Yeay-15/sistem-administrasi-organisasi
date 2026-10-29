<div class="mb-2 flex items-center justify-between gap-2">
    <h3 class="font-semibold text-slate-800 group-hover:text-blue-700 dark:text-white dark:group-hover:text-blue-400">{{ $t->name }}</h3>
    <span title="{{ $t->letter_type === 'A' ? 'Surat Internal' : 'Surat Eksternal' }}"
        class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $t->letter_type === 'A' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400' }}">
        {{ $t->letter_type === 'A' ? 'Internal (A)' : 'Eksternal (B)' }}
    </span>
</div>
<p class="flex-1 text-sm text-slate-500 dark:text-slate-400">{{ $t->description }}</p>
<p class="mt-3 text-xs font-semibold text-blue-600 dark:text-blue-400">Pakai template ini &rarr;</p>
