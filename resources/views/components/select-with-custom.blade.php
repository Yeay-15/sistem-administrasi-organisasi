@props([
    'name',
    'options' => [],          // daftar nilai yang sudah ada (array/Collection)
    'selected' => '',
    'emptyLabel' => 'Tidak ada', // label untuk pilihan kosong
    'customLabel' => '+ Lainnya...', // pilihan untuk mengetik nilai baru
    'customPlaceholder' => 'Ketik nilai baru',
    'maxlength' => 100,
])

@php
    $selected = (string) ($selected ?? '');
    $list = collect($options)
        ->map(fn ($option) => (string) $option)
        ->when($selected !== '', fn ($items) => $items->push($selected))
        ->unique()
        ->values();

    $dropdownOptions = ['' => $emptyLabel]
        + $list->mapWithKeys(fn ($option) => [$option => $option])->all()
        + ['__custom' => $customLabel];
@endphp

{{--
    Dropdown bertema (x-dropdown-select) + opsi terakhir untuk mengetik nilai
    baru yang belum ada di daftar. Nilai akhirnya dikirim lewat <input hidden>
    bernama $name, jadi sisi Laravel tetap menerima string biasa.
--}}
<div x-data="{ val: @js($selected), custom: false }">
    <x-dropdown-select :placeholder="$emptyLabel" :selected="$selected" :options="$dropdownOptions"
        on-select="custom = (value === '__custom'); val = custom ? '' : value" />
    <input type="text" x-show="custom" x-cloak x-model="val" maxlength="{{ $maxlength }}" placeholder="{{ $customPlaceholder }}"
        x-effect="if (custom) $nextTick(() => $el.focus())"
        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
    <input type="hidden" name="{{ $name }}" :value="val">
</div>
