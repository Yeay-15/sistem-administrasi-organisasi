@props(['name' => 'dates', 'selected' => [], 'max' => 62])

{{-- Pemilih banyak tanggal + rentang tanggal.
     - Klik tanggal            : pilih / batal pilih
     - Shift + klik            : pilih rentang dari klik terakhir
     - Tombol "Mode rentang"   : klik tanggal awal lalu tanggal akhir (nyaman di HP)
     Mengirim <input name="{{ $name }}[]"> berformat YYYY-MM-DD, satu per tanggal. --}}
@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('katiberMultiDate', (initial, max) => ({
                selected: [],
                viewYear: 0,
                viewMonth: 0,
                rangeMode: false,
                anchor: null,
                last: null,
                warn: '',
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                    'September', 'Oktober', 'November', 'Desember'
                ],
                shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt',
                    'Nov', 'Des'
                ],

                init() {
                    this.selected = Array.from(new Set(initial || [])).sort();
                    const t = new Date();
                    const first = this.selected.length ? this.parse(this.selected[0]) : {
                        y: t.getFullYear(),
                        m: t.getMonth() + 1
                    };
                    this.viewYear = first.y;
                    this.viewMonth = first.m - 1;
                },

                pad(n) {
                    return String(n).padStart(2, '0');
                },
                iso(y, m, d) {
                    return y + '-' + this.pad(m) + '-' + this.pad(d);
                },
                parse(s) {
                    const p = s.split('-');
                    return {
                        y: +p[0],
                        m: +p[1],
                        d: +p[2]
                    };
                },
                addDays(s, n) {
                    const p = this.parse(s);
                    const dt = new Date(Date.UTC(p.y, p.m - 1, p.d + n));
                    return this.iso(dt.getUTCFullYear(), dt.getUTCMonth() + 1, dt.getUTCDate());
                },
                get todayIso() {
                    const t = new Date();
                    return this.iso(t.getFullYear(), t.getMonth() + 1, t.getDate());
                },
                get monthLabel() {
                    return this.months[this.viewMonth] + ' ' + this.viewYear;
                },

                get cells() {
                    const y = this.viewYear,
                        m = this.viewMonth;
                    const startDow = new Date(Date.UTC(y, m, 1)).getUTCDay();
                    const out = [];
                    for (let i = 0; i < 42; i++) {
                        const dt = new Date(Date.UTC(y, m, 1 - startDow + i));
                        out.push({
                            iso: this.iso(dt.getUTCFullYear(), dt.getUTCMonth() + 1, dt
                                .getUTCDate()),
                            day: dt.getUTCDate(),
                            inMonth: dt.getUTCMonth() === m,
                        });
                    }
                    return out;
                },

                prevMonth() {
                    if (this.viewMonth === 0) {
                        this.viewMonth = 11;
                        this.viewYear--;
                    } else {
                        this.viewMonth--;
                    }
                },
                nextMonth() {
                    if (this.viewMonth === 11) {
                        this.viewMonth = 0;
                        this.viewYear++;
                    } else {
                        this.viewMonth++;
                    }
                },
                goToday() {
                    const t = new Date();
                    this.viewYear = t.getFullYear();
                    this.viewMonth = t.getMonth();
                },

                isSelected(s) {
                    return this.selected.includes(s);
                },

                pick(cell, ev) {
                    this.warn = '';
                    const s = cell.iso;
                    if (this.rangeMode) {
                        if (!this.anchor) {
                            this.anchor = s;
                            return;
                        }
                        this.addRange(this.anchor, s);
                        this.anchor = null;
                        this.last = s;
                        return;
                    }
                    if (ev && ev.shiftKey && this.last) {
                        this.addRange(this.last, s);
                        this.last = s;
                        return;
                    }
                    if (this.isSelected(s)) {
                        this.selected = this.selected.filter((x) => x !== s);
                    } else if (this.selected.length >= max) {
                        this.warn = 'Maksimal ' + max + ' tanggal sekali input.';
                        return;
                    } else {
                        this.selected = this.selected.concat([s]).sort();
                    }
                    this.last = s;
                },

                addRange(a, b) {
                    if (a > b) {
                        const t = a;
                        a = b;
                        b = t;
                    }
                    const set = new Set(this.selected);
                    let cur = a,
                        guard = 0;
                    while (cur <= b && guard < 400) {
                        if (!set.has(cur)) {
                            if (set.size >= max) {
                                this.warn = 'Maksimal ' + max +
                                    ' tanggal sekali input; sisa rentang diabaikan.';
                                break;
                            }
                            set.add(cur);
                        }
                        cur = this.addDays(cur, 1);
                        guard++;
                    }
                    this.selected = Array.from(set).sort();
                },

                clear() {
                    this.selected = [];
                    this.anchor = null;
                    this.last = null;
                    this.warn = '';
                },

                fmt(s) {
                    const p = this.parse(s);
                    return p.d + ' ' + this.shortMonths[p.m - 1] + ' ' + p.y;
                },

                // Kelompokkan tanggal berurutan jadi rentang untuk ringkasan/chip.
                get groups() {
                    const out = [];
                    this.selected.forEach((s) => {
                        const g = out[out.length - 1];
                        if (g && this.addDays(g.end, 1) === s) {
                            g.end = s;
                            g.dates.push(s);
                        } else {
                            out.push({
                                start: s,
                                end: s,
                                dates: [s]
                            });
                        }
                    });
                    return out.map((g) => {
                        const a = this.parse(g.start),
                            b = this.parse(g.end);
                        let label;
                        if (g.start === g.end) label = this.fmt(g.start);
                        else if (a.m === b.m && a.y === b.y) label = a.d + '–' + b.d + ' ' +
                            this.shortMonths[a.m - 1] + ' ' + a.y;
                        else label = this.fmt(g.start) + ' – ' + this.fmt(g.end);
                        return {
                            key: g.start,
                            label: label,
                            count: g.dates.length,
                            dates: g.dates
                        };
                    });
                },

                removeGroup(g) {
                    this.selected = this.selected.filter((x) => !g.dates.includes(x));
                },
            }));
        });
    </script>
@endonce

<div x-data="katiberMultiDate(@js($selected), {{ (int) $max }})" {{ $attributes->class(['w-full']) }}>
    <template x-for="d in selected" :key="d">
        <input type="hidden" name="{{ $name }}[]" :value="d">
    </template>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
        {{-- Kalender --}}
        <div
            class="rounded-xl border border-slate-300 bg-white p-3 dark:border-slate-700 dark:bg-slate-800 md:col-span-3">
            <div class="mb-2 flex items-center justify-between">
                <button type="button" @click="prevMonth()"
                    class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                    aria-label="Bulan sebelumnya">&larr;</button>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-slate-800 dark:text-white" x-text="monthLabel"></span>
                    <button type="button" @click="goToday()"
                        class="rounded-md px-2 py-0.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/10">Hari
                        ini</button>
                </div>
                <button type="button" @click="nextMonth()"
                    class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700"
                    aria-label="Bulan berikutnya">&rarr;</button>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-xs font-semibold text-slate-400">
                <template x-for="h in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']" :key="h">
                    <div class="py-1" x-text="h"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1">
                <template x-for="c in cells" :key="c.iso">
                    <button type="button" @click="pick(c, $event)" x-text="c.day"
                        class="h-9 rounded-lg text-sm transition select-none"
                        :class="{
                            'bg-blue-600 font-semibold text-white hover:bg-blue-700': isSelected(c.iso),
                            'text-slate-800 hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-slate-700': !
                                isSelected(c.iso) && c.inMonth,
                            'text-slate-300 hover:bg-slate-50 dark:text-slate-600 dark:hover:bg-slate-700/50': !
                                isSelected(c.iso) && !c.inMonth,
                            'ring-2 ring-amber-400': anchor === c.iso,
                            'border border-blue-400': c.iso === todayIso && !isSelected(c.iso),
                        }"></button>
                </template>
            </div>

            <div
                class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3 dark:border-slate-700">
                <label
                    class="flex cursor-pointer items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-300">
                    <input type="checkbox" x-model="rangeMode" @change="anchor = null"
                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                    Mode rentang
                    <span x-show="rangeMode" x-cloak class="text-amber-600 dark:text-amber-400"
                        x-text="anchor ? '- pilih tanggal akhir' : '- pilih tanggal awal'"></span>
                </label>
                <button type="button" @click="clear()" x-show="selected.length" x-cloak
                    class="text-xs font-semibold text-red-600 hover:underline dark:text-red-400">Hapus semua</button>
            </div>
        </div>

        {{-- Ringkasan pilihan --}}
        <div
            class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 dark:border-slate-700 dark:bg-slate-800/40 md:col-span-2">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Tanggal dipilih: <span x-text="selected.length"></span>
            </p>
            <p x-show="!selected.length" class="mt-2 text-sm text-slate-400 dark:text-slate-500">
                Klik tanggal di kalender. Tahan <kbd class="rounded border px-1 text-xs">Shift</kbd> atau nyalakan Mode
                rentang untuk memilih rentang.
            </p>
            <div class="mt-2 flex flex-wrap gap-2">
                <template x-for="g in groups" :key="g.key">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                        <span x-text="g.label"></span>
                        <span x-show="g.count > 1" class="font-normal opacity-70"
                            x-text="'(' + g.count + ' hari)'"></span>
                        <button type="button" @click="removeGroup(g)" class="ml-0.5 hover:text-red-600"
                            aria-label="Hapus">&times;</button>
                    </span>
                </template>
            </div>
            <p x-show="warn" x-cloak class="mt-2 text-xs text-amber-600 dark:text-amber-400" x-text="warn"></p>
            <p x-show="selected.length > 1" x-cloak class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                Akan dibuat <b x-text="selected.length"></b> agenda terpisah (satu per tanggal), masing-masing punya
                absensi sendiri.
            </p>
        </div>
    </div>
</div>
