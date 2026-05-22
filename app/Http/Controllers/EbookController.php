<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use App\Models\Program;
use App\Models\ProgramCourse;
use Illuminate\Http\Request;

class EbookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ebook::with(['program', 'course']);
    
        // Apply filters based on dropdown selections
        if ($request->filled('title')) {
            $query->where('title', $request->title);
        }
    
        if ($request->filled('author')) {
            $query->where('author', $request->author);
        }
    
        if ($request->filled('year')) {
            $query->where('publication_year', $request->year);
        }
    
        if ($request->filled('publisher')) {
            $query->where('publisher', $request->publisher);
        }
    
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
    
        // New: program & course filters
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
    
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
    
        // Get filtered and paginated result
        $ebooks = $query->latest()->paginate(10);
    
        return view('ebooks.index', [
            'ebooks' => $ebooks,
            'allTitles' => Ebook::select('title')->distinct()->pluck('title'),
            'allAuthors' => Ebook::select('author')->distinct()->pluck('author'),
            'allYears' => Ebook::select('publication_year')->distinct()->pluck('publication_year'),
            'allPublishers' => Ebook::select('publisher')->distinct()->pluck('publisher'),
            'allSources' => Ebook::select('source')->distinct()->pluck('source'),
            'allPrograms' => Program::orderBy('program_name')->get(),
            'allCourses' => ProgramCourse::orderBy('course_name')->get(),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = Program::all();
        $courses = ProgramCourse::all(); // assuming ProgramCourse holds program_id + course_name

        return view('ebooks.create', compact('programs', 'courses'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'program_id' => 'nullable|exists:programs,id',
            'course_id' => 'nullable|exists:program_courses,id',
        ]);

        // Handle "all" for program
        if ($request->program_id === 'all') {
            $validated['program_id'] = null;
        }

        Ebook::create($validated);

        return redirect()->route('ebooks.index')
            ->with('success', 'E-Book added successfully!');
    }

    public function update(Request $request, $id)
    {
        $ebook = Ebook::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publication_year' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'program_id' => 'nullable|exists:programs,id',
            'course_id' => 'nullable|exists:program_courses,id',
        ]);

        // Handle "all" for program
        if ($request->program_id === 'all') {
            $validated['program_id'] = null;
        }

        $ebook->update($validated);

        return redirect()->route('ebooks.index')
            ->with('success', 'E-Book updated successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Ebook $ebook)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ebook = Ebook::findOrFail($id);

        // get all programs
        $programs = Program::all();

        // default courses = all courses
        $courses = ProgramCourse::all()->map(function ($course) {
            return [
                'id' => $course->id,
                'name' => $course->course_name,
            ];
        });

        return view('ebooks.edit', compact('ebook', 'programs', 'courses'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ebook $ebook)
    {
        $ebook->delete();

        return redirect()->route('ebooks.index')->with('success', 'E-Book deleted successfully!');
    }

    public function getCourses($programId)
    {
        if ($programId === 'all') {
            $courses = ProgramCourse::all()->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->course_name,
                ];
            });
        } else {
            $courses = ProgramCourse::whereHas('year.program', function ($query) use ($programId) {
                $query->where('id', $programId);
            })->get()->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->course_name,
                ];
            });
        }

        return response()->json($courses);
    }


}
