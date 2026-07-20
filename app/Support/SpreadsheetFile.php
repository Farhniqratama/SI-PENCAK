<?php

namespace App\Support;

use RuntimeException;

class SpreadsheetFile
{
    public static function rowsFromUpload($file): array
    {
        if (!$file || !$file->isValid()) {
            throw new RuntimeException('File tidak valid atau gagal diunggah.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $path = $file->getRealPath();

        if (in_array($extension, ['csv', 'txt'], true)) {
            return self::rowsFromCsv($path);
        }

        if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);

            return $spreadsheet->getActiveSheet()->toArray();
        }

        throw new RuntimeException('Import .xlsx/.xls membutuhkan PhpSpreadsheet. Gunakan file .csv atau install phpoffice/phpspreadsheet.');
    }

    public static function streamCsv(string $filename, array $headers, iterable $rows)
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private static function rowsFromCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw new RuntimeException('File CSV tidak bisa dibaca.');
        }

        $rows = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (count($row) === 1 && str_contains($row[0] ?? '', ';')) {
                $row = str_getcsv($row[0], ';');
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }
}
