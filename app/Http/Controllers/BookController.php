<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Prospectus;
use App\Models\Program;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();
        $query2 = Program::query();

        // Filtering logic
        if ($request->has('status') && in_array($request->status, ['Available', 'Borrowed'])) {
            $query->where('availability', $request->status);
        }

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('program')) {
            $query2->where('program_name', $request->program);
        }

        // Get unique courses and years dynamically for dropdowns
        $programs = Program::orderBy('program_name')->get();
        $courses = Book::when($request->program, fn($q) => $q->where('program', $request->program))
            ->select('course')->distinct()->orderBy('course')->pluck('course');
        $years = Book::when($request->program, fn($q) => $q->where('program', $request->program))
            ->when($request->course, fn($q) => $q->where('course', $request->course))
            ->select('year')->distinct()->orderBy('year')->pluck('year');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_statement', 'like', "%$search%")
                    ->orWhere('rfid', 'like', "%$search%")
                    ->orWhere('call_number', 'like', "%$search%")
                    ->orWhere('main_author', 'like', "%$search%")
                    ->orWhere('course', 'like', "%$search%")
                    ->orWhere('year', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%");
            });
        }

        $courses = Book::select('course')
            ->distinct()
            ->whereNotNull('course')
            ->orderBy('course')
            ->pluck('course');

        $books = $query->orderBy('title_statement')->paginate(10);

        return view('books.index', compact('books', 'courses', 'years', 'programs'));
    }


    public function landingPage(Request $request)
    {
        $query = Book::query();
    
        // Apply filter if course is selected
        if ($request->filled('course') && $request->course !== 'all') {
            $query->where('course', $request->course);
        }
    
        // carousel (not filtered — always recent books)
        $carouselBooks = Book::orderBy('created_at', 'desc')->take(12)->get();
    
        // filtered + paginated
        $books = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
    
        // distinct course list for sidebar
        $courses = Book::select('course')
            ->whereNotNull('course')
            ->where('course', '<>', '')
            ->distinct()
            ->orderBy('course')
            ->pluck('course');
    
        // program list
        $programs = Program::orderBy('program_code')->pluck('program_code', 'id');
    
        return view('books.landing', compact('books', 'courses', 'programs', 'carouselBooks'));
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('book.index')->with('success', 'Book deleted successfully!');
    }

    public function create()
    {
        $courses = Prospectus::select('course')->distinct()->orderBy('course')->pluck('course');
        $programs = Program::orderBy('program_name')->get();
        return view('books.create', compact('courses', 'programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'control_no' => 'nullable|string|max:255',
            'date_time_stamp' => 'nullable|string|max:255',
            'fixed_length_data' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'cataloging_source_a' => 'nullable|string|max:255',
            'cataloging_source_b' => 'nullable|string|max:255',
            'cataloging_source_e' => 'nullable|string|max:255',
            'main_author' => 'nullable|string|max:255',
            'title_statement' => 'nullable|string|max:255',
            'title_author' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:255',
            'pub_place' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'pub_year' => 'nullable|string|max:255',
            'pages' => 'nullable|string|max:255',
            'illustrations' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'volume' => 'nullable|string|max:255',
            'content_type' => 'nullable|string|max:255',
            'content_code' => 'nullable|string|max:255',
            'media_type' => 'nullable|string|max:255',
            'media_code' => 'nullable|string|max:255',
            'carrier_type' => 'nullable|string|max:255',
            'carrier_code' => 'nullable|string|max:255',
            'series_title' => 'nullable|string|max:255',
            'general_note' => 'nullable|string|max:255',
            'bibliography_note' => 'nullable|string|max:255',
            'source_vendor' => 'nullable|string|max:255',
            'source_date' => 'nullable|string|max:255',
            'subject_topic' => 'nullable|string|max:255',
            'subject_form' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'library_name' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
            'call_number' => 'nullable|string|max:255',
            'accession_no' => 'nullable|string|max:255',
            'created_at' => 'nullable|string|max:255',
            'updated_at' => 'nullable|string|max:255',
            'barcode' => 'nullable|unique:books,barcode',
            'rfid' => 'nullable|unique:books,rfid',
            'availability' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Handle cover image
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            \File::copy(
                storage_path('app/public/' . $coverPath),
                public_path('storage/' . $coverPath)
            );
        }

        // Create book (NO program field here)
        $book = Book::create([
            'control_no' => $request->control_no,
            'date_time_stamp' => $request->date_time_stamp,
            'fixed_length_data' => $request->fixed_length_data,
            'isbn' => $request->isbn,
            'price' => $request->price,
            'cataloging_source_a' => $request->cataloging_source_a,
            'cataloging_source_b' => $request->cataloging_source_b,
            'cataloging_source_e' => $request->cataloging_source_e,
            'main_author' => $request->main_author,
            'title_statement' => $request->title_statement,
            'title_author' => $request->title_author,
            'edition' => $request->edition,
            'pub_place' => $request->pub_place,
            'publisher' => $request->publisher,
            'pub_year' => $request->pub_year,
            'pages' => $request->pages,
            'illustrations' => $request->illustrations,
            'size' => $request->size,
            'volume' => $request->volume,
            'content_type' => $request->content_type,
            'content_code' => $request->content_code,
            'media_type' => $request->media_type,
            'media_code' => $request->media_code,
            'carrier_type' => $request->carrier_type,
            'carrier_code' => $request->carrier_code,
            'series_title' => $request->series_title,
            'general_note' => $request->general_note,
            'bibliography_note' => $request->bibliography_note,
            'source_vendor' => $request->source_vendor,
            'source_date' => $request->source_date,
            'subject_topic' => $request->subject_topic,
            'subject_form' => $request->subject_form,
            'genre' => $request->genre,
            'library_name' => $request->library_name,
            'section' => $request->section,
            'call_number' => $request->call_number,
            'accession_no' => $request->accession_no,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
            'barcode' => $request->barcode,
            'rfid' => $request->rfid,
            'availability' => 'Available',
            'year' => $request->year,
            'course' => $request->course,
            'cover_image' => $coverPath,
        ]);

        // Attach programs via pivot
        if ($request->has('program_ids')) {
            $book->programs()->attach($request->program_ids);
        }

        return redirect()->route('book.index')->with('success', 'Book added successfully!');
    }


    public function show($id)
    {
        $book = Book::findOrFail($id);
        return view('books.show', compact('book'));
    }

    public function edit($id)
    {
        $book = Book::with('programs')->findOrFail($id);
        $programs = Program::all(); // list for dropdown
        return view('books.edit', compact('book', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'control_no' => 'nullable|string|max:255',
            'date_time_stamp' => 'nullable|string|max:255',
            'fixed_length_data' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'cataloging_source_a' => 'nullable|string|max:255',
            'cataloging_source_b' => 'nullable|string|max:255',
            'cataloging_source_e' => 'nullable|string|max:255',
            'main_author' => 'nullable|string|max:255',
            'title_statement' => 'nullable|string|max:255',
            'title_author' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:255',
            'pub_place' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'pub_year' => 'nullable|string|max:255',
            'pages' => 'nullable|string|max:255',
            'illustrations' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'volume' => 'nullable|string|max:255',
            'content_type' => 'nullable|string|max:255',
            'content_code' => 'nullable|string|max:255',
            'media_type' => 'nullable|string|max:255',
            'media_code' => 'nullable|string|max:255',
            'carrier_type' => 'nullable|string|max:255',
            'carrier_code' => 'nullable|string|max:255',
            'series_title' => 'nullable|string|max:255',
            'general_note' => 'nullable|string|max:255',
            'bibliography_note' => 'nullable|string|max:255',
            'source_vendor' => 'nullable|string|max:255',
            'source_date' => 'nullable|string|max:255',
            'subject_topic' => 'nullable|string|max:255',
            'subject_form' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'library_name' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
            'call_number' => 'nullable|string|max:255',
            'accession_no' => 'nullable|string|max:255',
            'created_at' => 'nullable|string|max:255',
            'updated_at' => 'nullable|string|max:255',
            'barcode' => 'nullable|unique:books,barcode,' . $id,
            'rfid' => 'nullable|unique:books,rfid,' . $id,
            'year' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            // ❌ remove single program validation (we use many-to-many now)
            // 'program' => 'nullable|string|max:255',
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'exists:programs,id',
        ]);

        $data = $request->only([
            'control_no',
            'date_time_stamp',
            'fixed_length_data',
            'isbn',
            'price',
            'cataloging_source_a',
            'cataloging_source_b',
            'cataloging_source_e',
            'main_author',
            'title_statement',
            'title_author',
            'edition',
            'pub_place',
            'publisher',
            'pub_year',
            'pages',
            'illustrations',
            'size',
            'volume',
            'content_type',
            'content_code',
            'media_type',
            'media_code',
            'carrier_type',
            'carrier_code',
            'series_title',
            'general_note',
            'bibliography_note',
            'source_vendor',
            'source_date',
            'subject_topic',
            'subject_form',
            'genre',
            'library_name',
            'section',
            'call_number',
            'accession_no',
            'created_at',
            'updated_at',
            'barcode',
            'rfid',
            'year',
            'course',
            // ❌ don’t include program single field
        ]);

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');

            \File::copy(
                storage_path('app/public/' . $coverPath),
                public_path('storage/' . $coverPath)
            );

            $data['cover_image'] = $coverPath;
        }

        // ✅ First update book fields
        $book->update($data);

        // ✅ Then sync many-to-many programs
        if ($request->has('program_ids')) {
            $book->programs()->sync($request->program_ids);
        } else {
            $book->programs()->sync([]); // clear if none selected
        }

        return redirect()->route('book.index')->with('success', 'Book updated successfully!');
    }


    public function getYears(Request $request)
    {
        $program = $request->program;
        $years = Book::where('program', $program)
            ->select('year')->distinct()->orderBy('year')->pluck('year');
        return response()->json($years);
    }

    public function getCourses(Request $request)
    {
        $program = $request->program;
        $year = $request->year;
        $courses = Book::where('program', $program)
            ->where('year', $year)
            ->select('course')->distinct()->orderBy('course')->pluck('course');
        return response()->json($courses);
    }

    public function downloadBookReport()
    {
        // Count total books per title
        $booksByTitle = Book::select('title_statement')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('title_statement')
            ->orderBy('title_statement')
            ->get();

        $totalBooks = $booksByTitle->sum('total');

        // Get all subjects grouped by course
        $books = DB::table('books')
            ->select('course', 'title_statement')
            ->groupBy('course', 'title_statement')
            ->orderBy('course')
            ->orderBy('title_statement')
            ->get();

        $groupedBooks = $books->groupBy('course');

        // Pass both variables to the view
        $pdf = Pdf::loadView('pdf.book_report', compact('booksByTitle', 'totalBooks', 'groupedBooks'));

        return $pdf->download('book_report.pdf');
    }
}