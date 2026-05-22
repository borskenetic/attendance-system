<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit E-Book</title>
    <link rel="stylesheet" href="{{ asset('public/css/ebooks/edit.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-sm">
            <h1 class="mb-4">Edit E-Book</h1>

            <form action="{{ route('ebooks.update', $ebook->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">📖 Title</label>
                    <input type="text" name="title" value="{{ old('title', $ebook->title) }}" class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">✍ Author</label>
                    <input type="text" name="author" value="{{ old('author', $ebook->author) }}" class="form-control"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">📆 Publication Year</label>
                    <input type="text" name="publication_year"
                        value="{{ old('publication_year', $ebook->publication_year) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">🏢 Publisher</label>
                    <input type="text" name="publisher" value="{{ old('publisher', $ebook->publisher) }}"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">📚 Source</label>
                    <input type="text" name="source" value="{{ old('source', $ebook->source) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">🔗 Link</label>
                    <input type="url" name="link" value="{{ old('link', $ebook->link) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">🎓 Program</label>
                    <select id="program" name="program_id" class="form-select">
                        <option value="">-- All Programs --</option>
                        @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="mb-3">
                    <label class="form-label">📘 Course</label>
                    <select id="course" name="course_id" class="form-select">
                        <option value="">-- Select Course --</option>
                    </select>
                </div>



                <div class="d-flex justify-content-between">
                    <a href="{{ route('ebooks.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update E-Book</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const programSelect = document.getElementById("program");
            const courseSelect = document.getElementById("course");
            const allOptions = Array.from(courseSelect.options);

            programSelect.addEventListener("change", function () {
                const programId = this.value;
                courseSelect.innerHTML = "";

                const defaultOption = document.createElement("option");
                defaultOption.value = "";
                defaultOption.textContent = "-- Select Course --";
                courseSelect.appendChild(defaultOption);

                allOptions.forEach(option => {
                    if (!option.value) return;
                    if (!programId || option.dataset.program === programId) {
                        courseSelect.appendChild(option.cloneNode(true));
                    }
                });
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#program').on('change', function () {
            var programId = $(this).val() || 'all'; // default to 'all' if empty
            var courseDropdown = $('#course');
            courseDropdown.empty().append('<option value="">-- Select Course --</option>');

            $.get("{{ url('/program') }}/" + programId + "/courses", function (data) {
                data.forEach(function (course) {
                    courseDropdown.append(
                        $('<option>', {
                            value: course.id,
                            text: course.name
                        })
                    );
                });
            });
        });

        // Trigger once on page load so that "All Programs" loads all courses by default
        $('#program').trigger('change');
    </script>
    </script>
</body>

</html>