<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookLog;
use Carbon\Carbon;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExportController extends Controller
{
    public function exportBooks()
    {
        $fileName = 'books_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath);
        $writer->addRow(['Downloaded At' => Carbon::now()->format('Y-m-d H:i:s')]);
        $writer->addHeader(['ID', 'Barcode', 'Call Number', 'Title', 'Author', 'RFID', 'Status']);

        Book::chunk(500, fn($books) =>
            collect($books)->each(fn($book) =>
                $writer->addRow([
                    'ID' => $book->id,
                    'Barcode' => $book->barcode,
                    'Call Number' => $book->callno,
                    'Title' => $book->title_statement,
                    'Author' => $book->main_author,
                    'RFID' => $book->rfid,
                    'Status' => $book->availability,
                ])
            )
        );

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function exportTransactions()
    {
        $fileName = 'transactions_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath);
        $writer->addRow(['Downloaded At' => Carbon::now()->format('Y-m-d H:i:s')]);
        $writer->addHeader(['Barcode', 'Book Title', 'Author', 'RFID', 'Status', 'Timestamp']);

        BookLog::with('book')->chunk(500, fn($logs) =>
            collect($logs)->each(fn($log) =>
                $writer->addRow([
                    'Barcode' => $log->book->barcode ?? 'N/A',
                    'Book Title' => $log->book->title_statement ?? 'Unknown',
                    'Author' => $log->book->main_author ?? 'Unknown',
                    'RFID' => $log->book->rfid ?? 'N/A',
                    'Status' => $log->status,
                    'Timestamp' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                ])
            )
        );

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
