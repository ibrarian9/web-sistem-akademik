<?php

namespace App\Services\Finance;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    /**
     * Stream records to a CSV download with UTF-8 BOM.
     *
     * @param string $filename
     * @param array $headers
     * @param iterable $records
     * @param callable $rowCallback function($record, int $index): ?array
     * @return StreamedResponse
     */
    public static function stream(string $filename, array $headers, iterable $records, callable $rowCallback): StreamedResponse
    {
        $responseHeaders = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($headers, $records, $rowCallback) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            if (!empty($headers)) {
                fputcsv($file, $headers);
            }

            foreach ($records as $index => $item) {
                $row = $rowCallback($item, $index);
                if ($row !== null) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $responseHeaders);
    }
}
