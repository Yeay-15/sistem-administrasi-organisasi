@props([
    'name' => 'file_path',
    'accept' => '.pdf,.jpg,.jpeg,.png',
    'maxMb' => 5,
    'label' => 'Seret & lepas file ke sini',
    'hint' => 'PDF, JPG, JPEG, atau PNG. Maksimal 5MB.',
    'currentUrl' => null, // URL file yang sudah tersimpan (untuk halaman edit)
    'currentName' => null,
])

{{--
    Dropzone upload dengan animasi (drag-over, pop-in file terpilih, progress bar,
    preview gambar). Tetap memakai <input type="file"> asli yang tersembunyi,
    jadi validasi & request()->file() di Laravel tidak berubah.

    CSS ditulis di <style> sendiri (prefix kt-dz-) supaya langsung jalan tanpa
    perlu menjalankan ulang `npm run build`.
--}}

@once
    <style>
        .kt-dz {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: 1.75rem 1rem;
            border: 2px dashed rgb(203 213 225);
            border-radius: .875rem;
            background: rgb(248 250 252);
            text-align: center;
            cursor: pointer;
            overflow: hidden;
            transition: border-color .25s ease, background-color .25s ease, transform .25s cubic-bezier(.34, 1.56, .64, 1), box-shadow .25s ease;
        }
        .dark .kt-dz {
            border-color: rgb(71 85 105);
            background: rgba(30, 41, 59, .6);
        }
        .kt-dz:hover {
            border-color: rgb(96 165 250);
            background: rgb(239 246 255);
        }
        .dark .kt-dz:hover {
            border-color: rgb(96 165 250);
            background: rgba(59, 130, 246, .08);
        }
        .kt-dz:focus-visible {
            outline: 2px solid rgb(59 130 246);
            outline-offset: 2px;
        }
        .kt-dz.is-dragging {
            border-style: solid;
            border-color: rgb(59 130 246);
            background: rgb(219 234 254);
            transform: scale(1.02);
            box-shadow: 0 10px 30px -10px rgba(59, 130, 246, .45);
        }
        .dark .kt-dz.is-dragging {
            background: rgba(59, 130, 246, .18);
        }
        .kt-dz.is-error {
            border-color: rgb(248 113 113);
            animation: kt-dz-shake .4s ease;
        }
        .kt-dz-icon {
            width: 2.75rem;
            height: 2.75rem;
            color: rgb(100 116 139);
            transition: transform .3s cubic-bezier(.34, 1.56, .64, 1), color .25s ease;
        }
        .kt-dz:hover .kt-dz-icon {
            transform: translateY(-3px);
            color: rgb(59 130 246);
        }
        .kt-dz.is-dragging .kt-dz-icon {
            color: rgb(37 99 235);
            animation: kt-dz-bounce .8s ease-in-out infinite;
        }
        .kt-dz-title {
            font-size: .875rem;
            font-weight: 600;
            color: rgb(51 65 85);
        }
        .dark .kt-dz-title { color: rgb(226 232 240); }
        .kt-dz-sub {
            font-size: .75rem;
            color: rgb(100 116 139);
        }
        .dark .kt-dz-sub { color: rgb(148 163 184); }
        .kt-dz-link {
            color: rgb(37 99 235);
            font-weight: 600;
            text-decoration: underline;
        }
        .dark .kt-dz-link { color: rgb(96 165 250); }

        .kt-dz-file {
            display: flex;
            align-items: center;
            gap: .85rem;
            margin-top: .75rem;
            padding: .75rem;
            border: 1px solid rgb(226 232 240);
            border-radius: .75rem;
            background: white;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
        }
        .dark .kt-dz-file {
            border-color: rgb(51 65 85);
            background: rgb(15 23 42);
        }
        .kt-dz-thumb {
            flex: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3.25rem;
            height: 3.25rem;
            border-radius: .625rem;
            overflow: hidden;
            background: rgb(254 226 226);
            color: rgb(220 38 38);
            font-size: .65rem;
            font-weight: 800;
            letter-spacing: .04em;
        }
        .dark .kt-dz-thumb {
            background: rgba(220, 38, 38, .15);
            color: rgb(252 165 165);
        }
        .kt-dz-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .kt-dz-meta { min-width: 0; flex: 1; }
        .kt-dz-fname {
            font-size: .875rem;
            font-weight: 600;
            color: rgb(30 41 59);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .kt-dz-fname { color: white; }
        .kt-dz-fsize {
            font-size: .75rem;
            color: rgb(100 116 139);
        }
        .kt-dz-bar {
            height: .3rem;
            margin-top: .4rem;
            border-radius: 9999px;
            background: rgb(226 232 240);
            overflow: hidden;
        }
        .dark .kt-dz-bar { background: rgb(51 65 85); }
        .kt-dz-bar > span {
            display: block;
            height: 100%;
            width: 0;
            border-radius: inherit;
            background: linear-gradient(90deg, rgb(59 130 246), rgb(99 102 241));
            transition: width .7s cubic-bezier(.22, 1, .36, 1);
        }
        .kt-dz-bar > span.is-done { width: 100%; background: rgb(34 197 94); }
        .kt-dz-remove {
            flex: none;
            padding: .4rem;
            border-radius: .5rem;
            color: rgb(148 163 184);
            transition: background-color .2s ease, color .2s ease, transform .2s ease;
        }
        .kt-dz-remove:hover {
            background: rgb(254 226 226);
            color: rgb(220 38 38);
            transform: rotate(90deg);
        }
        .kt-dz-current {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .6rem;
            font-size: .8125rem;
            color: rgb(71 85 105);
        }
        .dark .kt-dz-current { color: rgb(148 163 184); }
        .kt-dz-current a {
            font-weight: 600;
            color: rgb(79 70 229);
            text-decoration: underline;
        }
        .dark .kt-dz-current a { color: rgb(129 140 248); }
        .kt-dz-error {
            margin-top: .5rem;
            font-size: .8125rem;
            color: rgb(239 68 68);
        }

        /* Transisi masuk/keluar kartu file */
        .kt-dz-pop-enter { transition: opacity .35s ease, transform .45s cubic-bezier(.34, 1.56, .64, 1); }
        .kt-dz-pop-start { opacity: 0; transform: translateY(10px) scale(.94); }
        .kt-dz-pop-end { opacity: 1; transform: translateY(0) scale(1); }
        .kt-dz-pop-leave { transition: opacity .2s ease, transform .2s ease; }

        @keyframes kt-dz-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes kt-dz-shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .kt-dz, .kt-dz *, .kt-dz-file, .kt-dz-file * { animation: none !important; transition: none !important; }
        }
    </style>
@endonce

<div x-data="{
        dragging: false,
        file: null,
        previewUrl: null,
        isImage: false,
        ext: '',
        error: '',
        done: false,
        maxBytes: {{ (int) $maxMb }} * 1024 * 1024,
        allowed: ['pdf', 'jpg', 'jpeg', 'png'],

        humanSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1024 / 1024).toFixed(2) + ' MB';
        },
        fail(msg) {
            this.error = msg;
            this.$refs.zone.classList.remove('is-error');
            void this.$refs.zone.offsetWidth; // restart animasi shake
            this.$refs.zone.classList.add('is-error');
            this.clear(); // kosongkan input supaya file tidak valid tidak ikut terkirim
        },
        pick(files) {
            if (!files || !files.length) return;
            const f = files[0];
            const ext = (f.name.split('.').pop() || '').toLowerCase();
            this.error = '';
            if (!this.allowed.includes(ext)) {
                return this.fail('Format .' + ext + ' tidak didukung. Gunakan PDF, JPG, JPEG, atau PNG.');
            }
            if (f.size > this.maxBytes) {
                return this.fail('Ukuran file ' + this.humanSize(f.size) + ' melebihi batas {{ (int) $maxMb }}MB.');
            }
            // Sinkronkan ke <input> asli supaya ikut ter-submit lewat form
            const dt = new DataTransfer();
            dt.items.add(f);
            this.$refs.input.files = dt.files;

            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.isImage = ['jpg', 'jpeg', 'png'].includes(ext);
            this.previewUrl = this.isImage ? URL.createObjectURL(f) : null;
            this.ext = ext;
            this.file = f;
            this.done = false;
            this.$refs.zone.classList.remove('is-error');
            // Progress bar mengisi penuh setelah kartu muncul
            setTimeout(() => { this.done = true; }, 80);
        },
        clear(resetInput = true) {
            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = null;
            this.file = null;
            this.done = false;
            if (resetInput) this.$refs.input.value = '';
        },
        drop(e) {
            this.dragging = false;
            this.pick(e.dataTransfer.files);
        }
    }"
    class="w-full">

    @if ($currentUrl)
        <div class="kt-dz-current">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
            </svg>
            <span>File saat ini:</span>
            <a href="{{ $currentUrl }}" target="_blank">{{ $currentName ?? 'Lihat berkas' }}</a>
        </div>
    @endif

    <div x-ref="zone" class="kt-dz" :class="{ 'is-dragging': dragging }" tabindex="0" role="button"
        aria-label="Unggah arsip surat"
        @click="$refs.input.click()"
        @keydown.enter.prevent="$refs.input.click()"
        @keydown.space.prevent="$refs.input.click()"
        @dragenter.prevent="dragging = true"
        @dragover.prevent="dragging = true"
        @dragleave.prevent="if (!$el.contains($event.relatedTarget)) dragging = false"
        @drop.prevent="drop($event)">

        <svg class="kt-dz-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
        </svg>

        <p class="kt-dz-title" x-text="dragging ? 'Lepaskan file di sini' : '{{ $label }}'"></p>
        <p class="kt-dz-sub">atau <span class="kt-dz-link">pilih dari perangkat</span></p>
        <p class="kt-dz-sub">{{ $hint }}</p>
    </div>

    {{-- Input asli (tersembunyi) --}}
    <input x-ref="input" type="file" name="{{ $name }}" accept="{{ $accept }}" class="hidden"
        @change="pick($event.target.files)">

    <p x-show="error" x-cloak x-text="error" class="kt-dz-error"></p>

    {{-- Kartu file terpilih --}}
    <div x-show="file" x-cloak
        x-transition:enter="kt-dz-pop-enter" x-transition:enter-start="kt-dz-pop-start" x-transition:enter-end="kt-dz-pop-end"
        x-transition:leave="kt-dz-pop-leave" x-transition:leave-start="kt-dz-pop-end" x-transition:leave-end="kt-dz-pop-start"
        class="kt-dz-file">
        <div class="kt-dz-thumb">
            <template x-if="isImage">
                <img :src="previewUrl" alt="Pratinjau">
            </template>
            <template x-if="!isImage">
                <span x-text="ext.toUpperCase()"></span>
            </template>
        </div>
        <div class="kt-dz-meta">
            <p class="kt-dz-fname" x-text="file ? file.name : ''" :title="file ? file.name : ''"></p>
            <p class="kt-dz-fsize" x-text="file ? humanSize(file.size) : ''"></p>
            <div class="kt-dz-bar"><span :class="{ 'is-done': done }" :style="done ? '' : 'width:0'"></span></div>
        </div>
        <button type="button" class="kt-dz-remove" @click.stop="clear()" aria-label="Hapus file terpilih">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
