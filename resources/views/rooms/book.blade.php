<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Study Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            transition: 0.2s;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .flatpickr-calendar.inline {
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">📚 Book a Study Room</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('room-reservations.store') }}">
        @csrf

        <!-- Room Selection -->
        <div class="mb-3">
            <label class="form-label fw-bold">Main Library Room*</label>
            <select name="room_id" class="form-select" required>
                <option value="">Select a Room</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date Picker -->
        <div class="mb-3">
            <label class="form-label fw-bold">Select Date*</label>
            <input type="text" id="datePicker" name="date" class="form-control" required>
        </div>

        <!-- Time Selection -->
        <div class="mb-3">
            <label class="form-label fw-bold">Start Time*</label>
            <div class="d-flex gap-2 align-items-center">
                <select id="start_time" name="start_time" class="form-select" required>
                    <option value="">Select Time</option>
                </select>
                <select id="start_ampm" name="start_ampm" class="form-select" required>
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">End Time*</label>
            <div class="d-flex gap-2 align-items-center">
                <select id="end_time" name="end_time" class="form-select" required>
                    <option value="">Select Time</option>
                </select>
                <select id="end_ampm" name="end_ampm" class="form-select" required>
                    <option value="AM">AM</option>
                    <option value="PM">PM</option>
                </select>
            </div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label fw-bold">Email*</label>
            <input type="email" name="patron_email" class="form-control" required>
        </div>

        <!-- Number of Students -->
        <div class="mb-3">
            <label class="form-label fw-bold">Number of Students*</label>
            <input type="number" name="number_of_students" id="numStudents" class="form-control"
                   placeholder="Enter number of students (max 20)" min="1" max="20" required>
        </div>
        
        <!-- Student Names -->
        <div id="studentFields"></div>


        <button type="submit" class="btn-submit mt-3 w-100">Submit</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // ✅ Calendar date picker
    flatpickr("#datePicker", {
        inline: true,
        minDate: "today",
        dateFormat: "Y-m-d",
        disableMobile: true
    });

    // ✅ Generate 30-min interval times
    document.addEventListener('DOMContentLoaded', function () {
        const startSelect = document.getElementById('start_time');
        const endSelect = document.getElementById('end_time');
        const times = [];

        for (let h = 1; h <= 12; h++) {
            times.push(`${h}:00`);
            times.push(`${h}:30`);
        }

        times.forEach(time => {
            const opt1 = document.createElement('option');
            opt1.value = time;
            opt1.textContent = time;
            startSelect.appendChild(opt1);

            const opt2 = document.createElement('option');
            opt2.value = time;
            opt2.textContent = time;
            endSelect.appendChild(opt2);
        });
    });

    // ✅ Dynamic student fields
    const numStudents = document.getElementById('numStudents');
    const studentFields = document.getElementById('studentFields');

    numStudents.addEventListener('input', function () {
        studentFields.innerHTML = '';

        const count = parseInt(this.value);
        if (!isNaN(count) && count > 0 && count <= 20) {
            for (let i = 1; i <= count; i++) {
                studentFields.innerHTML += `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Student ${i} Name*</label>
                        <input type="text" name="student_names[]" class="form-control" required>
                    </div>`;
            }
        } else if (count > 20) {
            studentFields.innerHTML = `
                <div class="alert alert-warning">
                    ⚠️ You can only add up to 20 students.
                </div>`;
        }
    });
</script>
</body>
</html>
