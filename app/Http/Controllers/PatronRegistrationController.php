<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Prospectus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatronRegistrationController extends Controller
{
    // ✅ Show registration form
    public function create()
    {
        $courses = Prospectus::select('course')->distinct()->pluck('course');
        return view('patrons.register', compact('courses'));
    }

    // ✅ Handle form submission (register patron)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'course' => 'required|string',
            'year' => 'required|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Save profile picture if provided
        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(base_path('images/profile_pictures'), $filename);
            $profilePath = 'images/profile_pictures/' . $filename;
        }

        // Generate sequential QR code for Student
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $lastCode = $lastStudent ? intval($lastStudent->qrcode) : 0;
        $newCode = str_pad($lastCode + 1, 8, '0', STR_PAD_LEFT);

        // Create Student record
        $student = Student::create([
            'lastname' => $validated['lastname'],
            'firstname' => $validated['firstname'],
            'course' => $validated['course'],
            'year' => $validated['year'],
            'profile_picture' => $profilePath,
            'qrcode' => $newCode,
        ]);

        // Create User record
        $user = User::create([
            'lname' => $validated['lastname'],
            'fname' => $validated['firstname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student', // default role
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! You can now log in.');
    }
}
