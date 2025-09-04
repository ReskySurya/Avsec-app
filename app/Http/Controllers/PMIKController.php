<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\User;
use App\Models\PMIKFolder;
use App\Models\PMIKDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PMIKController extends Controller
{

    public function index()
    {
        $folders = PMIKFolder::with('creator', 'documents')->latest()->paginate(12);

        return view('superadmin.pmik.index', compact('folders'));
    }

    public function create()
    {
        return view('superadmin.pmik.folders.create');
    }

    public function store(StoreFolderRequest $request)
    {
        $folder = PMIKFolder::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('pmik.index')
            ->with('success', 'Folder berhasil dibuat!');
    }

    public function show(PMIKFolder $folder)
    {
        $folder->load('documents.uploader');

        return view('superadmin.pmik.folders.show', compact('folder'));
    }

    public function edit(PMIKFolder $folder)
    {
        return view('superadmin.pmik.folders.edit', compact('folder'));
    }

    public function update(UpdateFolderRequest $request, PMIKFolder $folder)
    {
        $folder->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('pmik.index')
            ->with('success', 'Folder berhasil diperbarui!');
    }

    public function destroy(PMIKFolder $folder)
    {
        // Delete all documents in this folder first
        foreach ($folder->documents as $document) {
            if (file_exists(storage_path('app/' . $document->file_path))) {
                unlink(storage_path('app/' . $document->file_path));
            }
        }

        $folder->delete();

        return redirect()->route('pmik.index')
            ->with('success', 'Folder dan semua dokumen di dalamnya berhasil dihapus!');
    }

    public function createDocument(PMIKFolder $folder)
    {
        return view('documents.create', compact('folder'));
    }

    public function storeDocument(StoreDocumentRequest $request)
    {
        $folder = PMIKFolder::findOrFail($request->folder_id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';

            // 1. Tentukan direktori tujuan di dalam disk 'private' Anda
            $directory = 'documents/' . $folder->slug;

            // 2. Gunakan storeAs() untuk menyimpan file.
            // Method ini akan MENGEMBALIKAN path lengkap (termasuk 'private/...')
            // Argumen ketiga ('private') adalah nama disk Anda dari config/filesystems.php
            // Jika 'private' adalah disk default, Anda bisa menghapusnya.
            // Untuk amannya, kita bisa gunakan disk 'local' (storage/app) dan membuat folder private.
            $directory = 'private/documents/' . $folder->slug;
            $savedPath = $file->storeAs($directory, $fileName, 'local');


            // 3. Create document record
            PMIKDocument::create([
                'title' => $request->title,
                'original_name' => $file->getClientOriginalName(),
                'file_name' => $fileName,
                'file_path' => $savedPath, // GUNAKAN PATH YANG DIKEMBALIKAN LARAVEL
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'folder_id' => $request->folder_id,
                'uploaded_by' => Auth::id()
            ]);
        }

        return redirect()->route('folders.show', $folder)
            ->with('success', 'Dokumen berhasil diunggah!');
    }

    public function showDocument(PMIKDocument $document)
    {
        return view('documents.show', compact('document'));
    }

    public function view(PMIKDocument $document)
    {
        // CARA LAMA (rawan error di OS berbeda):
        // $filePath = storage_path('app/' . $document->file_path);

        // CARA BARU (DIREKOMENDASIKAN):
        // Gunakan Storage facade untuk mendapatkan path absolut yang benar
        // secara otomatis, tidak peduli apa sistem operasinya.
        // Ini mengasumsikan file Anda ada di disk 'local' (storage/app).
        $filePath = Storage::path($document->file_path);

        if (!Storage::exists($document->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
        ];

        if (auth()->user()->isOfficer()) {
            $headers['X-Frame-Options'] = 'DENY';
            $headers['Content-Security-Policy'] = "default-src 'self'; script-src 'none'; object-src 'none';";
        }

        return response()->file($filePath, $headers);
    }

    public function showViewer(PMIKDocument $document)
    {
        // Method ini tugasnya hanya menampilkan view 'viewer.blade.php'
        // dan mengirimkan data dokumen yang ingin ditampilkan.
        return view('pmik.documents.viewer', compact('document'));
    }

    public function download(PMIKDocument $document)
    {
        $filePath = storage_path('app/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($filePath, $document->original_name);
    }

    public function destroyDocument(PMIKDocument $document)
    {
        // Delete file from storage
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        $folderId = $document->folder_id;
        $document->delete();

        return redirect()->route('folders.show', $folderId)
            ->with('success', 'Dokumen berhasil dihapus!');
    }
}
