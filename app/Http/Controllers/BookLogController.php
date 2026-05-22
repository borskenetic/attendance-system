<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = BookLog::with('book');

        // Apply filters if present
        if ($request->filled('patron_name')) {
            $logs->where('patron_name', $request->patron_name);
        }

        if ($request->filled('book_title')) {
            $logs->whereHas('book', function ($query) use ($request) {
                $query->where('title_statement', $request->book_title);
            });
        }

        if ($request->filled('start_date')) {
            $logs->whereDate('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $logs->whereDate('timestamp', '<=', $request->end_date);
        }

        $logs = $logs->latest()->paginate(10);

        // Get unique patron names and book titles for filters
        $patronNames = BookLog::pluck('patron_name')->unique();
        $bookTitles = Book::pluck('title_statement')->unique();

        return view('books.logs', compact('logs', 'patronNames', 'bookTitles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rfid'        => 'required|string',
            'status'      => 'required|string|in:checked_out,checked_in',
            'patron_name' => 'required|string|max:255',
        ]);

        $book = Book::where('rfid', $request->rfid)->first();

        if (!$book) {
            return back()->with('error', 'Book not found.');
        }

        $lastTransaction = BookLog::where('book_id', $book->id)->latest()->first();

        if ($request->status === 'checked_out' && $book->availability === 'Borrowed') {
            return back()->with('error', 'This book is already checked out and cannot be borrowed again.');
        }

        if ($request->status === 'checked_in' && $book->availability === 'Available') {
            return back()->with('error', 'This book is already checked in.');
        }

        $newStatus = $request->status === 'checked_out' ? 'Checked Out' : 'Checked In';
        $book->availability = $request->status === 'checked_out' ? 'Borrowed' : 'Available';

        BookLog::create([
            'book_id'     => $book->id,
            'patron_name' => $request->patron_name,
            'status'      => $newStatus,
            'timestamp'   => Carbon::now('Asia/Manila'),
        ]);

        $book->save();

        return back()->with('success', "Book has been {$newStatus} successfully!");
    }
}
