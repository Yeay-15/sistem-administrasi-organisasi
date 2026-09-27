@props([
    'name' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => '-- Pilih --',
    'required' => false,
    'onSelect' => null,
    'dynamicRequired' => null, // ekspresi Alpine mentah, mis. "positionCategory === 'Ketua Bidang'"
    'disabled' => false,
])

@php
    // Menormalkan $options jadi array asosiatif seragam [['value'=>..,'label'=>..], ...].
    // Mendukung dua format pemanggilan:
    //   :options="['Aktif' => 'Aktif', 'Nonaktif' => 'Nonaktif']"                 (value => label sederhana)
    //   :options="$divisions->pluck('name', 'id')"                               (dari Eloquent collection)
    $normalizedOptions = [];
    foreach ($options as $key => $label) {
        $normalizedOptions[] = ['value' => (string) $key, 'label' => (string) $label];
    }
@endphp

{{--
    Dropdown kustom bertema Tailwind (menggantikan tampilan daftar opsi
    bawaan browser yang tidak bisa di-styling) sekaligus tetap punya
    <select> asli tersembunyi supaya validasi, old(), dan request()
    di sisi Laravel tidak perlu berubah sama sekali.

    - Pakai atribut `name` untuk field form biasa (submit otomatis lewat
      <select> tersembunyi yang disinkronkan reaktif).
    - Untuk dropdown yang murni kontrol tampilan & terhubung ke state
      Alpine form lain (mis. memicu logika tambahan saat berubah),
      jangan isi `name`, lalu pakai `on-select="namaVar = value; fungsiLain()"`.
--}}
<div
    x-data="katiberSelect(@js($normalizedOptions), @js((string) $selected))"
    @click.outside="open = false"
    @keydown.escape="open = false"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    @if ($name)
        <select name="{{ $name }}" class="hidden" tabindex="-1" aria-hidden="true" @if ($required) required @endif
            @if ($dynamicRequired) :required="{{ $dynamicRequired }}" @endif>
            <option value=""></option>
            @foreach ($normalizedOptions as $opt)
                <option value="{{ $opt['value'] }}" :selected="value === @js($opt['value'])">{{ $opt['label'] }}</option>
            @endforeach
        </select>
    @endif

    <button type="button" @if ($disabled) title="{{ is_string($disabled) ? $disabled : '' }}" @else @click="open = !open" @endif
        @if ($disabled) disabled @endif
        class="flex w-full items-center justify-between gap-2 rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-left text-sm text-slate-800 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        <span class="truncate" :class="!value && 'text-slate-400 dark:text-slate-500'"
            x-text="selectedLabel || @js($placeholder)"></span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-150" :class="open && 'rotate-180'">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <div x-show="open" x-cloak x-transition.opacity.duration.100ms
        class="absolute z-30 mt-1.5 max-h-60 w-full overflow-auto rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg dark:border-slate-700 dark:bg-slate-800">
        <template x-if="options.length === 0">
            <p class="px-3 py-2 text-sm text-slate-400 dark:text-slate-500">Tidak ada pilihan.</p>
        </template>
        <template x-for="opt in options" :key="opt.value">
            <button type="button" @click="choose(opt.value); {{ $onSelect }}"
                class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm transition"
                :class="opt.value === value
                    ? 'bg-blue-50 font-medium text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'
                    : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'">
                <span class="truncate" x-text="opt.label"></span>
                <svg x-show="opt.value === value" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2.5" stroke="currentColor" class="h-4 w-4 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </button>
        </template>
    </div>
</div>
