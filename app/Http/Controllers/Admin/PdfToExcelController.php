<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class PdfToExcelController extends Controller
{
    public function index()
    {
        return view('admin.pdf-excel.index');
    }

    public function upload(Request $request)
    {
        // Aumentar límites de PHP para uploads
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '12M');
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '256M');

        \Log::info('PDF Upload attempt', [
            'has_file' => $request->hasFile('pdf_file'),
            'all_files' => $request->allFiles(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size')
        ]);

        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ], [
            'pdf_file.required' => 'Debe seleccionar un archivo PDF.',
            'pdf_file.mimes' => 'El archivo debe ser un PDF válido.',
            'pdf_file.max' => 'El archivo no puede ser mayor a 10MB.',
        ]);

        try {
            $pdfFile = $request->file('pdf_file');
            
            \Log::info('PDF File details', [
                'name' => $pdfFile->getClientOriginalName(),
                'size' => $pdfFile->getSize(),
                'is_valid' => $pdfFile->isValid(),
                'error' => $pdfFile->getError()
            ]);
            
            // Verificar que el archivo se subió correctamente
            if (!$pdfFile->isValid()) {
                \Log::error('PDF File not valid', ['error' => $pdfFile->getErrorMessage()]);
                return back()->withErrors(['error' => 'Error al subir el archivo. Intente nuevamente.']);
            }

            // Crear directorio si no existe
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
                \Log::info('Created temp directory', ['path' => $tempDir]);
            }

            // Guardar archivo PDF temporalmente
            $fileName = 'pdf_' . time() . '.pdf';
            $pdfPath = $pdfFile->storeAs('temp', $fileName, 'local');
            $fullPdfPath = storage_path('app/' . $pdfPath);

            \Log::info('PDF Stored', [
                'path' => $pdfPath,
                'full_path' => $fullPdfPath,
                'exists' => file_exists($fullPdfPath)
            ]);

            // Verificar que el archivo se guardó
            if (!file_exists($fullPdfPath)) {
                \Log::error('PDF File not saved', ['path' => $fullPdfPath]);
                return back()->withErrors(['error' => 'Error al guardar el archivo temporalmente.']);
            }

            // Procesar PDF
            $pilotos = $this->processPdf($fullPdfPath);

            \Log::info('PDF Processed', ['pilots_found' => count($pilotos)]);

            if (empty($pilotos)) {
                Storage::disk('local')->delete($pdfPath);
                return back()->withErrors(['error' => 'No se encontraron pilotos en el PDF. Verifique el formato del archivo.']);
            }

            // Generar Excel
            $excelPath = $this->generateExcel($pilotos);

            \Log::info('Excel Generated', ['path' => $excelPath]);

            // Limpiar archivo temporal
            Storage::disk('local')->delete($pdfPath);

            return response()->download($excelPath, 'pilotos_categoria_club.xlsx')->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('PDF Upload Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }
    }

    private function processPdf($pdfPath)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = $pdf->getText();
        $lines = explode("\n", $text);

        // Variables de control
        $pilotos = [];
        $categoria = '';
        $nombre = '';

        // Extraer datos
        foreach ($lines as $line) {
            $line = trim($line);

            // Detectar categoría
            if (preg_match('/^[A-ZÁÉÍÓÚÑ ]{5,} \d+ Corredores?$/u', $line)) {
                $categoria = preg_replace('/\s+\d+ Corredores?$/', '', $line);
                continue;
            }

            // Detectar nombre (líneas que empiezan con *)
            if (strpos($line, '*') === 0) {
                $nombre = trim(ltrim($line, '* '));
                continue;
            }

            // Detectar club (líneas con mayúsculas después del nombre)
            if (!empty($nombre) && preg_match('/[A-Z]{2,}/', $line)) {
                $club = $line;
                $pilotos[] = [
                    'Nombre' => $nombre,
                    'Club' => $club,
                    'Categoría' => $categoria,
                ];
                $nombre = '';
            }
        }

        return $pilotos;
    }

    private function generateExcel($pilotos)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Pilotos");

        // Configurar encabezados
        $headers = ['Nombre', 'Club', 'Categoría'];
        $sheet->fromArray($headers, null, 'A1');

        // Estilo para encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE0E0E0']
            ]
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Agregar datos
        $row = 2;
        foreach ($pilotos as $piloto) {
            $sheet->setCellValue("A$row", $piloto['Nombre']);
            $sheet->setCellValue("B$row", $piloto['Club']);
            $sheet->setCellValue("C$row", $piloto['Categoría']);
            $row++;
        }

        // Autoajustar columnas
        foreach (['A', 'B', 'C'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Guardar archivo
        $fileName = 'pilotos_categoria_club_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        // Crear directorio si no existe
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }

    public function uploadAjax(Request $request)
    {
        // Aumentar límites de PHP para uploads
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '12M');
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '256M');

        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ], [
            'pdf_file.required' => 'Debe seleccionar un archivo PDF.',
            'pdf_file.mimes' => 'El archivo debe ser un PDF válido.',
            'pdf_file.max' => 'El archivo no puede ser mayor a 10MB.',
        ]);

        try {
            $pdfFile = $request->file('pdf_file');
            
            if (!$pdfFile->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el archivo. Intente nuevamente.'
                ], 400);
            }

            // Crear directorio si no existe
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $fileName = 'pdf_ajax_' . time() . '.pdf';
            $pdfPath = $pdfFile->storeAs('temp', $fileName, 'local');
            $fullPdfPath = storage_path('app/' . $pdfPath);

            if (!file_exists($fullPdfPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el archivo temporalmente.'
                ], 500);
            }

            $pilotos = $this->processPdf($fullPdfPath);
            
            Storage::disk('local')->delete($pdfPath);

            if (empty($pilotos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron pilotos en el PDF. Verifique el formato del archivo.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $pilotos,
                'message' => 'PDF procesado correctamente. Se encontraron ' . count($pilotos) . ' pilotos.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadExcel(Request $request)
    {
        try {
            $pilotos = $request->input('pilotos', []);
            
            if (empty($pilotos)) {
                return response()->json(['success' => false, 'message' => 'No hay datos para exportar'], 400);
            }

            $excelPath = $this->generateExcel($pilotos);
            
            return response()->download($excelPath, 'pilotos_categoria_club.xlsx')->deleteFileAfterSend();

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar Excel: ' . $e->getMessage()], 500);
        }
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
        ], [
            'csv_file.required' => 'Debe seleccionar un archivo CSV.',
            'csv_file.mimes' => 'El archivo debe ser un CSV válido.',
            'csv_file.max' => 'El archivo no puede ser mayor a 5MB.',
        ]);

        try {
            $csvFile = $request->file('csv_file');
            
            if (!$csvFile->isValid()) {
                return back()->withErrors(['error' => 'Error al subir el archivo. Intente nuevamente.']);
            }

            // Leer archivo CSV
            $pilotos = $this->processCsv($csvFile->path());

            if (empty($pilotos)) {
                return back()->withErrors(['error' => 'No se encontraron pilotos en el CSV. Verifique el formato del archivo.']);
            }

            // Generar Excel
            $excelPath = $this->generateExcel($pilotos);

            return response()->download($excelPath, 'pilotos_categoria_club.xlsx')->deleteFileAfterSend();

        } catch (\Exception $e) {
            \Log::error('CSV Upload Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }
    }

    private function processCsv($csvPath)
    {
        $pilotos = [];
        
        // Intentar diferentes codificaciones
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'UTF-16'];
        $content = null;
        
        foreach ($encodings as $encoding) {
            $content = @file_get_contents($csvPath);
            if ($content !== false) {
                // Convertir a UTF-8 si no lo está
                if ($encoding !== 'UTF-8') {
                    $content = mb_convert_encoding($content, 'UTF-8', $encoding);
                }
                break;
            }
        }

        if ($content === null || $content === false) {
            throw new \Exception("No se pudo leer el archivo CSV convertido");
        }

        return $content;
    }
}