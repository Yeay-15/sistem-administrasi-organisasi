@php
    $key = $field['key'];
    $type = $field['type'] ?? 'text';
    $inputClass = 'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500 dark:[color-scheme:dark]';
    $value = old($key, $defaults[$key] ?? '');
@endphp
<div class="{{ $type === 'textarea' ? 'md:col-span-2' : '' }}">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $field['label'] }}</label>
    @if ($type === 'textarea')
        <textarea name="{{ $key }}" rows="2" @required($field['required'] ?? true) placeholder="{{ $field['placeholder'] ?? '' }}" class="{{ $inputClass }}">{{ $value }}</textarea>
    @else
        <input type="{{ in_array($type, ['date', 'time']) ? $type : 'text' }}" name="{{ $key }}" value="{{ $value }}"
            @required($field['required'] ?? true) placeholder="{{ $field['placeholder'] ?? '' }}" class="{{ $inputClass }}">
    @endif
    @if (! empty($field['help']))
        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ $field['help'] }}</p>
    @endif
    @error($key)
        <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
