<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesImportTemplateExport;
use App\Exports\EmployeesListExport;
use App\Imports\EmployeesImport;
use App\Services\BulkIdCardService;
use App\Models\Employee;
use App\Support\RespondsWithHydratablePartial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    use RespondsWithHydratablePartial;

    /**
     * Show all faculty (employees with role_id = 2)
     */
    public function index(Request $request)
    {
        $faculty = $this->filteredEmployeesQuery($request)
            ->orderBy('lastname', 'asc')
            ->paginate(15)
            ->withQueryString();

        $departments = Cache::remember('employees.departments', 600, fn () =>
            Employee::where('role_id', 2)
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->distinct()
                ->orderBy('department')
                ->pluck('department')
        );

        $positions = Cache::remember('employees.positions', 600, fn () =>
            Employee::where('role_id', 2)
                ->whereNotNull('position')
                ->where('position', '!=', '')
                ->distinct()
                ->orderBy('position')
                ->pluck('position')
        );

        return $this->hydratableResponse(
            $request,
            'employees.index',
            'employees.partials.list-table',
            compact('faculty', 'departments', 'positions'),
        );
    }

    private function filteredEmployeesQuery(Request $request)
    {
        $query = Employee::with('role')->where('role_id', 2);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        return $query;
    }

    public function downloadImportTemplate()
    {
        return Excel::download(
            new EmployeesImportTemplateExport,
            'employees_import_template.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        Excel::import(new EmployeesImport, $request->file('file'));

        return back()->with('success', 'Employees imported successfully.');
    }

    public function bulkDownloadIds(Request $request, BulkIdCardService $bulkIds)
    {
        $employees = $this->filteredEmployeesQuery($request)->orderBy('lastname')->get();

        if ($employees->isEmpty()) {
            return back()->with('error', 'No employees match the current filters.');
        }

        if ($employees->count() > BulkIdCardService::MAX_BULK_IDS) {
            return back()->with(
                'error',
                'Too many employees ('.$employees->count().'). Narrow filters to '.BulkIdCardService::MAX_BULK_IDS.' or fewer.'
            );
        }

        set_time_limit(max(120, $employees->count() * 3));

        return $bulkIds->downloadEmployeesZip($employees);
    }

    public function export(Request $request)
    {
        $paginated = $this->filteredEmployeesQuery($request)
            ->orderBy('lastname', 'asc')
            ->paginate(15)
            ->appends($request->query());

        $rows = collect($paginated->items());

        if ($rows->isEmpty()) {
            return back()->with('error', 'No employees on this page to export.');
        }

        $filename = 'employees_page_'.$paginated->currentPage().'_'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(new EmployeesListExport($rows), $filename);
    }

    /**
     * Show the edit form for an employee
     */
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }
    
    public function create()
    {
        return view('employees.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:255|unique:employees,employee_id',
            'department' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'employee_number' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|string|max:20',
            'tin_id_number' => 'nullable|string|max:255',
            'philhealth_number' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:5',
            'sss_number' => 'nullable|string|max:255',
            'hdmf_number' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string|max:255',
            'employee_signature' => 'nullable|string',
            'formal_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        // Force Faculty role
        $validated['role_id'] = 2;
    
        // Generate QR automatically (same style as students)
        if (!empty($validated['employee_id'])) {
            $validated['qrcode'] = 'E-' . $validated['employee_id'];
        }
    
        // Handle profile picture
        if ($request->hasFile('formal_picture')) {
            $file = $request->file('formal_picture');
            $filename = time() . '_profile_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
    
            if (!file_exists(base_path('images/formal_pictures'))) {
                mkdir(base_path('images/formal_pictures'), 0755, true);
            }
    
            $file->move(base_path('images/formal_pictures'), $filename);
            $validated['formal_picture'] = 'images/formal_pictures/' . $filename;
        }
    
        // Handle signature (base64)
        if (!empty($validated['employee_signature']) && str_starts_with($validated['employee_signature'], 'data:')) {
            [$meta, $contents] = explode(',', $validated['employee_signature'], 2);
    
            $ext = 'png';
            if (preg_match('/data:image\/(jpeg|jpg)/i', $meta)) $ext = 'jpg';
    
            $sigName = time() . '_sig.' . $ext;
    
            if (!file_exists(base_path('images/signatures'))) {
                mkdir(base_path('images/signatures'), 0755, true);
            }
    
            file_put_contents(base_path('images/signatures/' . $sigName), base64_decode($contents));
            $validated['employee_signature'] = 'images/signatures/' . $sigName;
        }
    
        Employee::create($validated);
    
        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }


    /**
     * Update employee record
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
    
        $validated = $request->validate([
            'employee_id' => 'nullable|string|max:255|unique:employees,employee_id,' . $employee->id,
            'department' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'employee_number' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'sex' => 'nullable|string|max:20',
            'tin_id_number' => 'nullable|string|max:255',
            'philhealth_number' => 'nullable|string|max:255',
            'civil_status' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:5',
            'sss_number' => 'nullable|string|max:255',
            'hdmf_number' => 'nullable|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string|max:255',
            'employee_signature' => 'nullable|string',
            'formal_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        // 🔒 Force role to Faculty
        $validated['role_id'] = 2;
    
        /*
        |--------------------------------------------------------------------------
        | Regenerate QR Code if employee_id changed
        |--------------------------------------------------------------------------
        */
        if (!empty($validated['employee_id'])) {
            $validated['qrcode'] = 'E-' . $validated['employee_id'];
        }
    
        /*
        |--------------------------------------------------------------------------
        | Handle Profile Picture Upload
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('formal_picture')) {
    
            // Delete old picture
            if (!empty($employee->formal_picture) && file_exists(base_path($employee->formal_picture))) {
                unlink(base_path($employee->formal_picture));
            }
    
            $file = $request->file('formal_picture');
            $filename = time() . '_profile_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
    
            if (!file_exists(base_path('images/formal_pictures'))) {
                mkdir(base_path('images/formal_pictures'), 0755, true);
            }
    
            $file->move(base_path('images/formal_pictures'), $filename);
            $validated['formal_picture'] = 'images/formal_pictures/' . $filename;
        }
    
        /*
        |--------------------------------------------------------------------------
        | Handle Signature (Base64)
        |--------------------------------------------------------------------------
        */
        if (!empty($validated['employee_signature']) &&
            str_starts_with($validated['employee_signature'], 'data:')) {
    
            // Delete old signature
            if (!empty($employee->employee_signature) &&
                file_exists(base_path($employee->employee_signature))) {
                unlink(base_path($employee->employee_signature));
            }
    
            [$meta, $contents] = explode(',', $validated['employee_signature'], 2);
    
            $ext = 'png';
            if (preg_match('/data:image\/(jpeg|jpg)/i', $meta)) {
                $ext = 'jpg';
            }
    
            $sigName = time() . '_sig.' . $ext;
    
            if (!file_exists(base_path('images/signatures'))) {
                mkdir(base_path('images/signatures'), 0755, true);
            }
    
            file_put_contents(
                base_path('images/signatures/' . $sigName),
                base64_decode($contents)
            );
    
            $validated['employee_signature'] = 'images/signatures/' . $sigName;
        }
    
        /*
        |--------------------------------------------------------------------------
        | Update Employee
        |--------------------------------------------------------------------------
        */
        $employee->update($validated);
    
        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }
    /**
     * Delete employee
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return back()->with('success', 'Employee deleted successfully.');
    }
}
