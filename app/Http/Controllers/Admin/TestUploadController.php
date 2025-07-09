<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestUploadController extends Controller
{
    public function index()
    {
        return view('admin.test-upload');
    }

    public function upload(Request $request)
    {
        Log::info('Upload attempt started', [
            'has_file' => $request->hasFile('pdf_file'),
            'files' => $request->allFiles(),
            'all_input' => $request->all()
        ]);

        try {
            // Verificar si el archivo existe en la request
            if (!$request->hasFile('pdf_file')) {
                Log::error('No file in request');
                return back()->withErrors(['error' => 'No se recibió ningún archivo.']);
            }

            $file = $request->file('pdf_file');
            
            Log::info('File details', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError(),
                'error_message' => $file->getErrorMessage()
            ]);

            // Verificar errores de upload
            if (!$file->isValid()) {
                $error = $file->getErrorMessage();
                Log::error('File upload error', ['error' => $error]);
                return back()->withErrors(['error' => 'Error al subir archivo: ' . $error]);
            }

            // Intentar mover el archivo
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $fileName = 'test_' . time() . '.pdf';
            $success = $file->storeAs('temp', $fileName, 'local');
            
            Log::info('File store result', [
                'success' => $success,
                'path' => storage_path('app/temp/' . $fileName),
                'exists' => file_exists(storage_path('app/temp/' . $fileName))
            ]);

            if ($success) {
                return back()->with('success', 'Archivo subido exitosamente: ' . $fileName);
            } else {
                return back()->withErrors(['error' => 'Error al guardar el archivo.']);
            }

        } catch (\Exception $e) {
            Log::error('Upload exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Excepción: ' . $e->getMessage()]);
        }
    }
}
