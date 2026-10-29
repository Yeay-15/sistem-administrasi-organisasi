<?php

namespace Database\Seeders;

use App\Models\LetterTemplate;
use Illuminate\Database\Seeder;

/**
 * Daftar template surat. Aman dijalankan berulang (updateOrCreate per `code`):
 *
 *   php artisan db:seed --class=LetterTemplateSeeder
 *
 * Menambah template baru = taruh file .docx di resources/letter_templates
 * (pakai token ${nama_token}, lihat LetterGenerator::TOKENS) lalu tambahkan satu
 * baris di bawah ini.
 *
 * number_pattern memakai: {no} {tipe} {romawi} {tahun}. Nomor DISIMPAN tanpa spasi
 * (sama seperti data hasil `surat:import`), sedangkan di dalam .docx tetap ditulis
 * "ACEH UTARA" dengan spasi.
 */
class LetterTemplateSeeder extends Seeder
{
    private const NO_PANITIA = '{no}/{tipe}/PAN-PEL/KATIBERLHOKSEUMAWE-ACEHUTARA/{romawi}/{tahun}';
    private const NO_ORGANISASI = '{no}/{tipe}/KATIBERLHOKSEUMAWE-ACEHUTARA/{romawi}/{tahun}';

    private const SIGN_PANITIA = ['ketua_panitia', 'sekretaris_panitia', 'ketua_umum'];

    public function run(): void
    {
        $templates = [
            [
                'code' => 'undangan-walikota',
                'name' => 'Undangan Wali Kota',
                'description' => 'Undangan hadir & memberi sambutan untuk Wali Kota Tebing Tinggi.',
                'letter_type' => 'B',
                'file_name' => 'undangan-walikota.docx',
                'destination_pattern' => 'Bapak {nama_penerima} (Wali Kota Tebing Tinggi)',
                'fields' => [
                    ['key' => 'nama_penerima', 'label' => 'Nama Wali Kota', 'type' => 'text', 'required' => true, 'placeholder' => 'Nama lengkap beserta gelar'],
                ],
            ],
            [
                'code' => 'undangan-dprd',
                'name' => 'Undangan Ketua DPRD',
                'description' => 'Undangan hadir & memberi sambutan untuk Ketua DPRD Kota Tebing Tinggi.',
                'letter_type' => 'B',
                'file_name' => 'undangan-dprd.docx',
                'destination_pattern' => 'Bapak {nama_penerima} (Ketua DPRD Kota Tebing Tinggi)',
                'fields' => [
                    ['key' => 'nama_penerima', 'label' => 'Nama Ketua DPRD', 'type' => 'text', 'required' => true, 'placeholder' => 'Nama lengkap beserta gelar'],
                ],
            ],
            [
                'code' => 'undangan-katiber-pusat',
                'name' => 'Undangan KATIBER Pusat',
                'description' => 'Undangan untuk Ketua Umum KATIBER Pusat (tidak ada isian tambahan).',
                'letter_type' => 'B',
                'file_name' => 'undangan-katiber-pusat.docx',
                'destination_pattern' => 'Ketua Umum KATIBER Pusat',
                'fields' => [],
            ],
            [
                'code' => 'undangan-pak-amir',
                'name' => 'Undangan Pak Amir',
                'description' => 'Undangan arahan & sambutan untuk Bapak Amiruddin Ketaren, S.Sos., M.Sc. (internal).',
                'letter_type' => 'A',
                'file_name' => 'undangan-pak-amir.docx',
                'destination_pattern' => 'Bapak Amiruddin Ketaren, S.Sos., M.Sc.',
                'fields' => [],
            ],
            [
                'code' => 'undangan-angkatan',
                'name' => 'Undangan Abang & Kakak Angkatan',
                'description' => 'Undangan untuk satu angkatan senior (internal).',
                'letter_type' => 'A',
                'file_name' => 'undangan-angkatan.docx',
                'destination_pattern' => 'Abang dan Kakak Angkatan {angkatan}',
                'fields' => [
                    ['key' => 'angkatan', 'label' => 'Angkatan', 'type' => 'text', 'required' => true, 'placeholder' => 'mis. 2022', 'help' => 'Tampil sebagai "Abang dan Kakak Angkatan ...".'],
                ],
            ],
            [
                'code' => 'undangan-paguyuban',
                'name' => 'Undangan Paguyuban',
                'description' => 'Undangan untuk Ketua Umum paguyuban/organisasi daerah lain.',
                'letter_type' => 'B',
                'file_name' => 'undangan-paguyuban.docx',
                'destination_pattern' => 'Ketua Umum {nama_paguyuban}',
                'fields' => [
                    ['key' => 'nama_paguyuban', 'label' => 'Nama Paguyuban', 'type' => 'text', 'required' => true, 'placeholder' => 'mis. Paguyuban Mahasiswa Sibolga'],
                ],
            ],
        ];

        foreach ($templates as $i => $t) {
            LetterTemplate::updateOrCreate(
                ['code' => $t['code']],
                $t + [
                    'category' => LetterTemplate::CATEGORY_COMMITTEE,
                    'subject' => 'Undangan',
                    'number_pattern' => self::NO_PANITIA,
                    'signatory_label' => 'Ketua Panitia, Sekretaris Panitia, Ketua Umum',
                    'signers' => self::SIGN_PANITIA,
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ]
            );
        }

        // Surat organisasi (tidak terikat kepanitiaan)
        LetterTemplate::updateOrCreate(
            ['code' => 'permohonan-audiensi'],
            [
                'name' => 'Permohonan Audiensi',
                'description' => 'Permohonan audiensi ke pejabat/instansi, ditandatangani Ketua & Sekretaris Umum.',
                'category' => LetterTemplate::CATEGORY_ORGANIZATION,
                'letter_type' => 'B',
                'subject' => 'Permohonan Audiensi',
                'file_name' => 'permohonan-audiensi.docx',
                'number_pattern' => self::NO_ORGANISASI,
                'destination_pattern' => '{nama_penerima} ({jabatan_penerima})',
                'signatory_label' => 'Ketua Umum, Sekretaris Umum',
                'signers' => ['ketua_umum', 'sekretaris_umum'],
                'fields' => [
                    ['key' => 'nama_penerima', 'label' => 'Nama Penerima', 'type' => 'text', 'required' => true, 'placeholder' => 'Nama lengkap beserta gelar', 'help' => 'Tampil sebagai "Yth. Bapak ...".'],
                    ['key' => 'jabatan_penerima', 'label' => 'Jabatan Penerima', 'type' => 'text', 'required' => true, 'placeholder' => 'mis. Rektor Universitas Malikussaleh'],
                    ['key' => 'nama_instansi', 'label' => 'Nama Instansi/Lembaga', 'type' => 'text', 'required' => true, 'placeholder' => 'mis. Universitas Malikussaleh'],
                    ['key' => 'latar_belakang', 'label' => 'Latar Belakang/Keperluan Audiensi', 'type' => 'textarea', 'required' => true, 'help' => 'Kalimat: "Sehubungan dengan ____, kami bermaksud mengajukan permohonan audiensi..."'],
                    ['key' => 'tujuan_utama', 'label' => 'Tujuan Utama Audiensi', 'type' => 'textarea', 'required' => true, 'help' => 'Kalimat: "...sebagai bentuk silaturahmi sekaligus ____."'],
                    ['key' => 'topik', 'label' => 'Topik yang Ingin Disampaikan', 'type' => 'textarea', 'required' => true, 'help' => 'Kalimat: "Audiensi ini diharapkan dapat menjadi wadah untuk ____, mempererat hubungan..."'],
                    ['key' => 'harapan_lain', 'label' => 'Harapan/Keperluan Lain', 'type' => 'textarea', 'required' => true, 'help' => 'Kalimat: "...serta ____."'],
                    ['key' => 'hari_tanggal', 'label' => 'Tanggal Audiensi yang Diusulkan', 'type' => 'date', 'required' => true],
                    ['key' => 'jam_mulai', 'label' => 'Jam Mulai', 'type' => 'time', 'required' => true],
                    ['key' => 'tempat', 'label' => 'Tempat/Lokasi', 'type' => 'text', 'required' => true],
                ],
                'sort_order' => 7,
                'is_active' => true,
            ]
        );
    }
}
