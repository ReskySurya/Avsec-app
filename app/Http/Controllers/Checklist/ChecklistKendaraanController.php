<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;

class ChecklistKendaraanController extends Controller
{
    public function indexChecklistKendaraan()
    {
        // 1. Ambil semua item dari database
        $items = ChecklistItem::orderBy('category')->orderBy('id')->get();

        // 2. Kelompokkan item berdasarkan tipe kendaraan (mobil/motor)
        $groupedItems = $items->groupBy('type');

        // 3. Format data untuk checklist mobil sesuai struktur yang diharapkan view
        $mobilItems = $groupedItems->get('mobil', collect());
        $mobilChecklist = $mobilItems->groupBy('category')
            ->map(function ($items, $categoryName) {
                return [
                    'name' => strtoupper($categoryName),
                    'items' => $items->map(fn($item) => ['id' => $item->id, 'name' => $item->name])->values()->all(),
                ];
            })
            ->values()
            ->map(function ($category, $index) {
                $category['letter'] = chr(65 + $index); // Menghasilkan A, B, C, ...
                return $category;
            })
            ->all();

        // 4. Format data untuk checklist motor
        $motorItems = $groupedItems->get('motor', collect());
        $motorChecklist = $motorItems->groupBy('category')
            ->map(function ($items, $categoryName) {
                return [
                    'name' => strtoupper($categoryName),
                    'items' => $items->map(fn($item) => ['id' => $item->id, 'name' => $item->name])->values()->all(),
                ];
            })
            ->values()
            ->all();

        // 5. Kirim data yang sudah diformat ke view
        return view('checklist.checklistKendaraan', compact('mobilChecklist', 'motorChecklist'));
    }

    public function store(){
        
    }
}
