<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda extends Model
{
    // Mengizinkan mass assignment untuk kolom-kolom ini
    protected $fillable = [
        'agenda_code',
        'name',
        'date',
        'type',
        'person_in_charge',
        'notes',
        'status',
    ];

    // Relasi One-to-Many: 1 Agenda memiliki banyak Absensi
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    /**
     * Menghasilkan data event kalender lengkap dengan warna (dipakai bersama oleh
     * kalender admin, cetak PDF kalender, dan kalender Portal Publik) supaya
     * skema warna per-divisi & gradient kolaborasi selalu konsisten di semua tempat.
     */
    public static function buildCalendarEvents($agendas)
    {
        return $agendas->map(function ($agenda) {
            $contextText = strtolower($agenda->name . ' ' . $agenda->person_in_charge);

            // Mapping of each division to its exact Tailwind CSS classes
            // We write the full class names so Tailwind JIT compiler doesn't purge them
            $divisionColors = [
                'kaderisasi' => ['bg' => 'bg-red-500', 'from' => 'from-red-500', 'to' => 'to-red-500', 'hex' => '#ef4444'],
                'korwil' => ['bg' => 'bg-orange-500', 'from' => 'from-orange-500', 'to' => 'to-orange-500', 'hex' => '#f97316'],
                'koordinator' => ['bg' => 'bg-orange-500', 'from' => 'from-orange-500', 'to' => 'to-orange-500', 'hex' => '#f97316'],
                'humas' => ['bg' => 'bg-emerald-500', 'from' => 'from-emerald-500', 'to' => 'to-emerald-500', 'hex' => '#10b981'],
                'hubungan masyarakat' => ['bg' => 'bg-emerald-500', 'from' => 'from-emerald-500', 'to' => 'to-emerald-500', 'hex' => '#10b981'],
                'infokom' => ['bg' => 'bg-cyan-500', 'from' => 'from-cyan-500', 'to' => 'to-cyan-500', 'hex' => '#06b6d4'],
                'informasi' => ['bg' => 'bg-cyan-500', 'from' => 'from-cyan-500', 'to' => 'to-cyan-500', 'hex' => '#06b6d4'],
                'minat' => ['bg' => 'bg-pink-500', 'from' => 'from-pink-500', 'to' => 'to-pink-500', 'hex' => '#ec4899'],
                'bakat' => ['bg' => 'bg-pink-500', 'from' => 'from-pink-500', 'to' => 'to-pink-500', 'hex' => '#ec4899'],
                'agama' => ['bg' => 'bg-emerald-600', 'from' => 'from-emerald-600', 'to' => 'to-emerald-600', 'hex' => '#059669'],
                'pendidikan' => ['bg' => 'bg-yellow-500', 'from' => 'from-yellow-500', 'to' => 'to-yellow-500', 'hex' => '#eab308'],
                'invent' => ['bg' => 'bg-slate-500', 'from' => 'from-slate-500', 'to' => 'to-slate-500', 'hex' => '#64748b'],
                'bph' => ['bg' => 'bg-blue-600', 'from' => 'from-blue-600', 'to' => 'to-blue-600', 'hex' => '#2563eb'],
                'badan pengurus harian' => ['bg' => 'bg-blue-600', 'from' => 'from-blue-600', 'to' => 'to-blue-600', 'hex' => '#2563eb'],
            ];

            // Find all matching divisions in the text
            $foundDivisions = [];
            foreach ($divisionColors as $keyword => $colors) {
                if (str_contains($contextText, $keyword)) {
                    // Use 'bg' as key to prevent duplicate color entries (e.g. Humas & Hubungan Masyarakat)
                    $foundDivisions[$colors['bg']] = $colors;
                }
            }

            // Re-index the array
            $foundDivisions = array_values($foundDivisions);

            $isCollab = str_contains($contextText, ' x ') ||
                str_contains($contextText, ' dan ') ||
                str_contains($contextText, '&') ||
                str_contains($contextText, 'kolaborasi');

            // Default color if nothing matches
            $colorClass = 'bg-blue-500';
            $colorStyle = 'background-color: #3b82f6;';

            // Determine final color: Dynamic Gradient if Collab, otherwise solid color
            if ($isCollab && count($foundDivisions) >= 2) {
                // Combine 'from' color of the first division and 'to' color of the second division
                $colorClass = 'bg-gradient-to-r ' . $foundDivisions[0]['from'] . ' ' . $foundDivisions[1]['to'];
                $colorStyle = 'background: linear-gradient(90deg, ' . $foundDivisions[0]['hex'] . ', ' . $foundDivisions[1]['hex'] . ');';
            } elseif (count($foundDivisions) > 0) {
                // Single division detected
                $colorClass = $foundDivisions[0]['bg'];
                $colorStyle = 'background-color: ' . $foundDivisions[0]['hex'] . ';';
            }

            return [
                'id' => $agenda->id,
                'title' => $agenda->name,
                'code' => $agenda->agenda_code,
                'type' => $agenda->type,
                'pic' => $agenda->person_in_charge,
                'status' => $agenda->status,
                'date' => \Carbon\Carbon::parse($agenda->date)->format('Y-m-d'),
                'colorClass' => $colorClass,
                'colorStyle' => $colorStyle,
            ];
        });
    }
}
