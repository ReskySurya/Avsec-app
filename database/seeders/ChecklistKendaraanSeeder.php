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
        // Hapus data lama untuk menghindari duplikat
        DB::table('checklist_items')->delete();

        $now = Carbon::now();

        // 1. Definisikan item untuk motor (4 kolom)
        $motorItems = [
            ['name' => 'Lampu Depan Pendek', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Depan Jauh', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Rem', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kanan Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kiri Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kanan Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu Sein Kiri Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rem Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rem Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kopling Tangan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kondisi Ban Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kondisi Ban Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tekanan Ban Depan', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tekanan Ban Belakang', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kondisi Body Motor', 'type' => 'motor', 'created_at' => $now, 'updated_at' => $now],
        ];

        // 2. Definisikan item untuk mobil (5 kolom, termasuk 'category')
        $mobilItems = [
            ['name' => 'Oli Mesin', 'category' => 'mesin', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Air Radiator', 'category' => 'mesin', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Air Wiper', 'category' => 'mesin', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Accu', 'category' => 'mesin', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rem Kaki dan Hand Rem', 'category' => 'mekanik', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lampu (Utama, Kota, Sein, Rem, Rotari)', 'category' => 'mekanik', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ban', 'category' => 'mekanik', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Radio Mobile', 'category' => 'mekanik', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Kursi', 'category' => 'lainlain', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Karpet Dasar', 'category' => 'lainlain', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Kabin', 'category' => 'lainlain', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kebersihan Bak Belakang', 'category' => 'lainlain', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Data Service Rutin', 'category' => 'lainlain', 'type' => 'mobil', 'created_at' => $now, 'updated_at' => $now],
        ];

        // 3. Lakukan insert secara terpisah
        if (!empty($motorItems)) {
            DB::table('checklist_items')->insert($motorItems);
        }

        if (!empty($mobilItems)) {
            DB::table('checklist_items')->insert($mobilItems);
        }
    }
}
