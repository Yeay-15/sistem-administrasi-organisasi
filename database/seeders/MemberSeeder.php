<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Division;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        // Mengubah semua key menjadi huruf kecil untuk menghindari masalah case-sensitive
        $divisions = Division::all()->keyBy(function ($item) {
            return strtolower($item->abbreviation);
        });

        // Data hasil ekstrak dari Excel
        $members = [
            ['name' => 'Yahya Ayyash Alfaruq Lubis', 'student_id' => '240120002', 'abbreviation' => 'BPH', 'position' => 'Ketua Umum', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Gilang Akbar Hadikosyah', 'student_id' => '240170133', 'abbreviation' => 'BPH', 'position' => 'Sekretaris Umum', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Azna Nurul Husna Siregar', 'student_id' => '240620014', 'abbreviation' => 'BPH', 'position' => 'Bendahara Umum', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Teguh Prasetiyo Dwifa', 'student_id' => '240130123', 'abbreviation' => 'Kader', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Risky Khairunnisa', 'student_id' => '250210143', 'abbreviation' => 'Kader', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Dendiy Wiranda', 'student_id' => '240310099', 'abbreviation' => 'Korwil', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rifaldi Haykal Purba', 'student_id' => '240310132', 'abbreviation' => 'Korwil', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Imam Try Wahyudi', 'student_id' => '240120105', 'abbreviation' => 'Humas', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Nur Arzety Balqis Saragih', 'student_id' => '250140092', 'abbreviation' => 'Humas', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Annisa Rai', 'student_id' => '240210147', 'abbreviation' => 'Infokom', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Jihan Sahila Azmi Sinaga', 'student_id' => '240210184', 'abbreviation' => 'Infokom', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rifky Satya Ramadhan', 'student_id' => '240120007', 'abbreviation' => 'Mikat', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Diva Aulia', 'student_id' => '240510248', 'abbreviation' => 'Mikat', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Mawar Jelita', 'student_id' => '202421009', 'abbreviation' => 'Agama', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Mhd Mahesyah Putra', 'student_id' => '250150147', 'abbreviation' => 'Agama', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Jihan Aqila Mutmainnah Rangkuti', 'student_id' => '240240236', 'abbreviation' => 'Pendidikan', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Aqillah Andini', 'student_id' => '250240148', 'abbreviation' => 'Pendidikan', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Khairul Amilin Hasibuan', 'student_id' => '240150027', 'abbreviation' => 'Invent', 'position' => 'Ketua Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Nursaumah', 'student_id' => '240620013', 'abbreviation' => 'Invent', 'position' => 'Sekretaris Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Anggi Aulia', 'student_id' => '250320006', 'abbreviation' => 'Agama', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Della Triismalia', 'student_id' => '250340035', 'abbreviation' => 'Agama', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Frisandy Dwi Permana', 'student_id' => '250310190', 'abbreviation' => 'Agama', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Mhd. Fadhil Az-Dzikri Nasution', 'student_id' => '240130060', 'abbreviation' => 'Agama', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rizky', 'student_id' => '240420104', 'abbreviation' => 'Agama', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Safira Khairani', 'student_id' => '250420070', 'abbreviation' => 'Agama', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Dimas Fahri Habibi', 'student_id' => '250120030', 'abbreviation' => 'Humas', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Mhd. Akbar Nasution', 'student_id' => '250410128', 'abbreviation' => 'Humas', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Muhammad Fahrezi', 'student_id' => '210240128', 'abbreviation' => 'Humas', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Muhammad Hafizzuddin', 'student_id' => '240240118', 'abbreviation' => 'Humas', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Nadin Rahmatika Piliang', 'student_id' => '240170079', 'abbreviation' => 'Humas', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rafly Izham Saragih', 'student_id' => '250330005', 'abbreviation' => 'Humas', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Widya Ayu Aurelya Purba', 'student_id' => '250130115', 'abbreviation' => 'Humas', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Adinda Tri Syfarina', 'student_id' => '250410020', 'abbreviation' => 'Infokom', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Dea Natasya', 'student_id' => '250210092', 'abbreviation' => 'Infokom', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Ivanna Nayla', 'student_id' => '250440061', 'abbreviation' => 'Infokom', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Muhammad Fauzan Purba', 'student_id' => '250150118', 'abbreviation' => 'Infokom', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Panji Hatta Mulia', 'student_id' => '240180009', 'abbreviation' => 'Infokom', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Salman Alfarizi', 'student_id' => '250120174', 'abbreviation' => 'Infokom', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Zaki Yardan Siregar', 'student_id' => '250180163', 'abbreviation' => 'Infokom', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Audria Nurkayla', 'student_id' => '250320118', 'abbreviation' => 'Invent', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Dewi Atika', 'student_id' => '250510109', 'abbreviation' => 'Invent', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Hilal Hariri Lubis', 'student_id' => '250150076', 'abbreviation' => 'Invent', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Muhammad Rauf', 'student_id' => '240150087', 'abbreviation' => 'Invent', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rayyan Khadafi', 'student_id' => '250110151', 'abbreviation' => 'Invent', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Razly Syaputra', 'student_id' => '240120057', 'abbreviation' => 'Invent', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Dinda Syafira', 'student_id' => '250150104', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Fahar Ramadani Damanik', 'student_id' => '250120133', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Ikhlasul Akmal', 'student_id' => '250110067', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'M Khoir Al Abi Nasution', 'student_id' => '250310057', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Muhammad Amirul Mughniy', 'student_id' => '250120150', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Nurhaliza Rahmani Putri', 'student_id' => '250140141', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Riahta Purba', 'student_id' => '250250097', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Sindi Aspiradila Saragih', 'student_id' => '250510074', 'abbreviation' => 'Kader', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Aisyila Nazlaa', 'student_id' => '250110024', 'abbreviation' => 'Korwil', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Jihan Luthfi Azzuhra', 'student_id' => '250510268', 'abbreviation' => 'Korwil', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Mhd. Hasbi Azhari Lubis', 'student_id' => '250120120', 'abbreviation' => 'Korwil', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Misrini Anjarwati', 'student_id' => '202431028', 'abbreviation' => 'Korwil', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rafhael Hutasoit', 'student_id' => '250310169', 'abbreviation' => 'Korwil', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Yola Agustina', 'student_id' => '240310162', 'abbreviation' => 'Korwil', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Adinda Syahfitri', 'student_id' => '240240149', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Afrian', 'student_id' => '250110231', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Alif Auriza', 'student_id' => '240170176', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Fahrul Ramadhan', 'student_id' => '250510414', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Fazillah Natasya Nasution', 'student_id' => '240510293', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Intan Aulia', 'student_id' => '250510033', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'M Haikal Arif', 'student_id' => '250240211', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Muhammad Rayyan Ananda Harahap', 'student_id' => '250120065', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Nabila Amanda', 'student_id' => '240440076', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Nindy Ayu Syafika', 'student_id' => '250510329', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Nurul Aini Nasution', 'student_id' => '250510144', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rassya Ayu Aulia', 'student_id' => '250420166', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Zhafira Shafa Aulia', 'student_id' => '250340012', 'abbreviation' => 'Mikat', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Azzahra Muthia Maliq', 'student_id' => '250260036', 'abbreviation' => 'Pendidikan', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Chairina Azraini Hasibuan', 'student_id' => '250240219', 'abbreviation' => 'Pendidikan', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Ricky Agus Salim', 'student_id' => '240510058', 'abbreviation' => 'Pendidikan', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Rival Fhatan', 'student_id' => 'PLCHLDR1000', 'abbreviation' => 'Pendidikan', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Sulastri', 'student_id' => '250320151', 'abbreviation' => 'Pendidikan', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
            ['name' => 'Syafira Dwi Andini', 'student_id' => '250620125', 'abbreviation' => 'Pendidikan', 'position' => 'Anggota Divisi', 'batch' => '2024', 'status' => 'Aktif', 'join_date' => '2026-08-20'],
        ];

        foreach ($members as $mem) {
            // Kita ubah $mem['abbreviation'] menjadi lowercase saat pencarian di array $divisions
            $abbr_lower = strtolower($mem['abbreviation']);

            if (isset($divisions[$abbr_lower])) {
                $div_id = $divisions[$abbr_lower]->id;

                Member::firstOrCreate(
                    ['student_id' => $mem['student_id']],
                    [
                        'name' => ucwords(strtolower($mem['name'])),
                        'division_id' => $div_id,
                        'position' => $mem['position'],
                        'batch' => $mem['batch'],
                        'status' => $mem['status'],
                        'join_date' => $mem['join_date']
                    ]
                );
            }
        }
    }
}
