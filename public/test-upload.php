<?php
// Diagnóstico de configuración PHP
echo "=== DIAGNÓSTICO DE CONFIGURACIÓN PHP ===\n";
echo "Upload max filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post max size: " . ini_get('post_max_size') . "\n";
echo "Max execution time: " . ini_get('max_execution_time') . "\n";
echo "Max input time: " . ini_get('max_input_time') . "\n";
echo "Memory limit: " . ini_get('memory_limit') . "\n";
echo "File uploads enabled: " . (ini_get('file_uploads') ? 'YES' : 'NO') . "\n";
echo "Max file uploads: " . ini_get('max_file_uploads') . "\n";

echo "\n=== VERIFICACIÓN DE DIRECTORIOS ===\n";
$tempDir = storage_path('app/temp');
echo "Temp directory: $tempDir\n";
echo "Temp directory exists: " . (file_exists($tempDir) ? 'YES' : 'NO') . "\n";
echo "Temp directory writable: " . (is_writable($tempDir) ? 'YES' : 'NO') . "\n";

$storageDir = storage_path('app');
echo "Storage directory: $storageDir\n";
echo "Storage directory writable: " . (is_writable($storageDir) ? 'YES' : 'NO') . "\n";

echo "\n=== VERIFICACIÓN DE EXTENSIONES ===\n";
echo "PDFParser available: " . (class_exists('Smalot\PdfParser\Parser') ? 'YES' : 'NO') . "\n";
echo "PhpSpreadsheet available: " . (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet') ? 'YES' : 'NO') . "\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "\n=== RESULTADO DEL TEST ===\n";
    echo "Upload error code: " . $_FILES['test_file']['error'] . "\n";
    
    $errors = [
        UPLOAD_ERR_OK => 'No error',
        UPLOAD_ERR_INI_SIZE => 'File too large (upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'File too large (form MAX_FILE_SIZE)',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    
    echo "Error description: " . ($errors[$_FILES['test_file']['error']] ?? 'Unknown error') . "\n";
    echo "File name: " . $_FILES['test_file']['name'] . "\n";
    echo "File size: " . $_FILES['test_file']['size'] . " bytes\n";
    echo "File type: " . $_FILES['test_file']['type'] . "\n";
    echo "Temp name: " . $_FILES['test_file']['tmp_name'] . "\n";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico PHP Upload</title>
</head>
<body>
    <h2>Test de Upload de Archivos</h2>
    <form method="post" enctype="multipart/form-data">
        <p>Selecciona un archivo PDF pequeño para probar:</p>
        <input type="file" name="test_file" accept=".pdf">
        <br><br>
        <button type="submit">Probar Upload</button>
    </form>
</body>
</html>
