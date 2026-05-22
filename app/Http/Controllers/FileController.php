<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $files = File::all();
        return view('files.index', compact('files'));
    }



    public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:pdf,doc,docx|max:20480',
        'folder' => 'nullable|string',
    ]);

    $file = $request->file('file');
    $folder = $request->input('folder') ?? 'default';
    $filename = $file->getClientOriginalName();

    // ✅ Create folder if it doesn't exist (on public disk)
    Storage::disk('public')->makeDirectory("files/{$folder}");

    // ✅ Store in: storage/app/public/files/{folder}
    $path = $file->storeAs("files/{$folder}", $filename, 'public');

    File::create([
        'folder' => $folder,
        'filename' => $filename,
        'filepath' => "public/{$path}", // ensure this path matches actual location
    ]);

    return back()->with('success', 'File uploaded successfully.');
}





    public function view($id)
    {
        $file = File::findOrFail($id);
        $path = storage_path('app/' . $file->filepath);

        if (!file_exists($path)) {
            return back()->with('error', 'File does not exist.');
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return response()->file($path); // view in browser
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return response()->download($path); // force download
        }

        return back()->with('error', 'Unsupported file type.');
    }



    public function download($id)
    {
        $file = File::findOrFail($id);
        $path = storage_path('app/' . $file->filepath);

        if (!file_exists($path)) {
            return back()->with('error', 'File does not exist: ' . $path);
        }

        $extension = strtolower(pathinfo($file->filename, PATHINFO_EXTENSION));
        $forcePdf = ($extension !== 'pdf'); // if it's not a PDF

        // 👉 Rename it as PDF only if needed
        $downloadName = $forcePdf
            ? pathinfo($file->filename, PATHINFO_FILENAME) . '.pdf'
            : $file->filename;

        return response()->download($path, $downloadName, [
            'Content-Type' => mime_content_type($path),
        ]);
    }




    public function delete($id)
    {
        $file = File::findOrFail($id);
        Storage::delete($file->filepath);
        $file->delete();
        return back()->with('success', 'File deleted.');
    }
}
