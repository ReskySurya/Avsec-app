<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ChecklistKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikat saat seeder dijalankan ulang
        DB::table('checklist_items')->delete();

        $now = Carbon::now();
        $items = [
            // Motor Items
            ['name' => 'Lampu Depan Pendek', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Depan Jauh', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Rem', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kanan Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kiri Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kanan Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kanan Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rem Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rem Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kopling Tangan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kondisi Ban Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kondisi Ban Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tekanan Ban Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tekanan Ban Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kondisi Body Motor', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],

            // Mobil Items
            ['name' => 'Oli Mesin', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Air Radiator', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Air Wiper', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Accu', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rem Kaki dan Hand Rem', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu (Utama, Kota, Sein, Rem, Rotari)', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ban', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Radio Mobile', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Kursi', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Karpet Dasar', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Kabin', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Bak Belakang', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Data Service Rutin', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('checklist_items')->insert($items);
    }
}
