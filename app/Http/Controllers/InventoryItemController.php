<?php

namespace App\Http\Controllers;

use App\Exports\InventoryItemsExport;
use App\Exports\InventoryItemsTemplate;
use App\Imports\InventoryItemsImport;
use App\Models\AuditLog;
use App\Models\InventoryItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InventoryItemController extends Controller
{
    /** Saran satuan di form (ditambah satuan yang sudah pernah dipakai di data). */
    private const DEFAULT_UNITS = [
        'buah', 'unit', 'set', 'pasang', 'lusin', 'pak', 'kotak', 'plastik',
        'gulung', 'lembar', 'botol', 'btl', 'tablet', 'g', 'kg',
    ];

    public function index(Request $request)
    {
        $query = InventoryItem::query();

        // 1. Logika Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        if ($request->status === 'expired') {
            $query->whereNotNull('expired_at')->whereDate('expired_at', '<', today());
        } elseif ($request->status === 'soon') {
            $query->whereNotNull('expired_at')
                ->whereDate('expired_at', '>=', today())
                ->whereDate('expired_at', '<=', today()->addDays(InventoryItem::EXPIRY_WARNING_DAYS));
        }

        // 2. Pengurutan (juga dipakai apa adanya oleh Excel/PDF di bawah, supaya
        // berkas yang diunduh selalu mengikuti urutan yang sedang aktif di layar).
        $sort = in_array($request->get('sort'), ['name', 'location', 'expiry', 'newest'], true)
            ? $request->get('sort')
            : 'name';

        match ($sort) {
            'location' => $query->orderBy('location')->orderBy('name'),
            'expiry' => $query->orderByRaw('expired_at is null')->orderBy('expired_at')->orderBy('name'),
            'newest' => $query->orderByDesc('added_at')->orderByDesc('id'),
            default => $query->orderBy('name'),
        };

        $items = $query->get();

        // 3. Export Excel
        if ($request->export === 'excel') {
            return Excel::download(new InventoryItemsExport($items), 'Data_Inventaris_KATIBER.xlsx');
        }

        // 4. Export PDF
        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('exports.inventory_items_pdf', ['items' => $items])
                ->setPaper('a4', 'portrait');

            return $pdf->download('Data_Inventaris_KATIBER.pdf');
        }

        // 5. Halaman HTML
        $locations = InventoryItem::whereNotNull('location')->distinct()->orderBy('location')->pluck('location');

        $stats = [
            'total' => InventoryItem::count(),
            'locations' => $locations->count(),
            'attention' => InventoryItem::whereNotNull('expired_at')
                ->whereDate('expired_at', '<=', today()->addDays(InventoryItem::EXPIRY_WARNING_DAYS))
                ->count(),
        ];

        $locationOptions = $locations->mapWithKeys(fn ($location) => [$location => $location])->all();

        return view('inventory_items.index', compact('items', 'sort', 'stats', 'locationOptions'));
    }

    public function importForm()
    {
        return view('inventory_items.import');
    }

    public function template()
    {
        return InventoryItemsTemplate::response();
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'on_duplicate' => ['required', 'in:skip,update'],
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xlsx atau .xls (gunakan template yang disediakan).',
            'file.max' => 'Ukuran file maksimal 5MB.',
            'file.uploaded' => 'File gagal diunggah. Coba lagi atau gunakan file yang lebih kecil.',
        ]);

        $import = (new InventoryItemsImport($request->file('file')->getRealPath()))->read();

        if ($import->fatal) {
            return back()->withInput($request->only('on_duplicate'))->withErrors(['file' => $import->fatal]);
        }

        if ($import->errors) {
            return back()
                ->withInput($request->only('on_duplicate'))
                ->with('import_errors', array_slice($import->errors, 0, 50))
                ->with('import_error_total', count($import->errors));
        }

        if (empty($import->rows)) {
            return back()->withInput($request->only('on_duplicate'))
                ->withErrors(['file' => 'Tidak ada baris data yang bisa diimpor. Isi data mulai dari baris ke-2.']);
        }

        $result = $import->save($request->input('on_duplicate'));

        AuditLog::record('Impor Inventaris', "Impor inventaris dari Excel: {$result['created']} barang baru, {$result['updated']} diperbarui, {$result['skipped']} dilewati");

        return redirect()->route('inventory-items.index')->with(
            'success',
            "Impor selesai: {$result['created']} barang baru, {$result['updated']} diperbarui, {$result['skipped']} dilewati karena sudah ada."
        );
    }

    public function create()
    {
        return view('inventory_items.create', $this->formOptions());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $item = InventoryItem::create($data);

        AuditLog::record('Tambah Inventaris', 'Menambahkan barang inventaris: ' . $item->name . ' (' . $item->quantity_with_unit . ')');

        return redirect()->route('inventory-items.index')->with('success', 'Barang inventaris berhasil ditambahkan.');
    }

    public function edit(InventoryItem $inventoryItem)
    {
        return view('inventory_items.edit', ['inventoryItem' => $inventoryItem] + $this->formOptions());
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $inventoryItem->update($data);

        AuditLog::record('Update Inventaris', 'Mengubah data barang inventaris: ' . $inventoryItem->name . ' (' . $inventoryItem->quantity_with_unit . ')');

        return redirect()->route('inventory-items.index')->with('success', 'Data barang inventaris berhasil diperbarui.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $name = $inventoryItem->name;

        $inventoryItem->delete();

        AuditLog::record('Delete Inventaris', 'Menghapus barang inventaris: ' . $name);

        return redirect()->route('inventory-items.index')->with('success', 'Barang inventaris berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'unit' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'added_at' => ['required', 'date'],
            'expired_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function attributes(): array
    {
        return [
            'name' => 'nama barang',
            'quantity' => 'jumlah',
            'unit' => 'satuan',
            'location' => 'tempat',
            'added_at' => 'tanggal ditambahkan',
            'expired_at' => 'tanggal kedaluwarsa',
            'notes' => 'keterangan',
        ];
    }

    /**
     * Pilihan dropdown Satuan di form: satuan bawaan + yang sudah pernah dipakai.
     */
    private function formOptions(): array
    {
        return [
            'units' => collect(self::DEFAULT_UNITS)
                ->merge(InventoryItem::whereNotNull('unit')->distinct()->pluck('unit'))
                ->unique()
                ->values(),
        ];
    }
}
