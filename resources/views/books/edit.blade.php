<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/css/books/edit.css') }}">


</head>

<body>
    <div class="edit-container">
        <div class="edit-header">📝 Edit Book</div>

        <form method="POST" action="{{ route('book.update', $book->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-section">
                        <label for="control_no">001</label>
                        <input type="text" name="control_no" class="form-control" value="{{ $book->control_no }}"
                            placeholder="Control No.">
                    </div>

                    <div class="form-section">
                        <label for="marc_timestamp">005</label>
                        <input type="datetime-local" name="date_time_stamp" class="form-control"
                            value="{{ $book->date_time_stamp }}" placeholder="Enter Date and Time Stamp">
                    </div>

                    <div class="form-section">
                        <label for="fixed_length_data">008</label>
                        <input type="text" name="fixed_length_data" class="form-control"
                            value="{{ $book->fixed_length_data }}" placeholder="Enter Fixed-Length Data">
                    </div>

                    <div class="form-section">
                        <label for="isbn">020 ‡a</label>
                        <input type="text" name="isbn" class="form-control" value="{{ $book->isbn }}"
                            placeholder="Enter ISBN">
                    </div>

                    <div class="form-section">
                        <label for="price">020 ‡c</label>
                        <input type="text" name="price" class="form-control" value="{{ $book->price }}"
                            placeholder="Enter Price">
                    </div>

                    <div class="form-section">
                        <label for="cataloging_source">040 ‡a</label>
                        <input type="text" name="cataloging_source_a" class="form-control"
                            value="{{ $book->cataloging_source_a }}" placeholder="Enter Cataloging Source">
                    </div>

                    <div class="form-section">
                        <label for="language">040 ‡b</label>
                        <input type="text" name="cataloging_source_b" class="form-control"
                            value="{{ $book->cataloging_source_b }}" placeholder="Enter Language">
                    </div>

                    <div class="form-section">
                        <label for="description_conventions">040 ‡e</label>
                        <input type="text" name="cataloging_source_e" value="rda" class="form-control"
                            value="{{ $book->cataloging_source_e }}" placeholder="Enter Description Conventions">
                    </div>

                    <div class="form-section">
                        <label for="main_author">100 ‡a</label>
                        <input type="text" name="main_author" class="form-control" value="{{ $book->main_author }}"
                            placeholder="Enter Main Author">
                    </div>

                    <div class="form-section">
                        <label for="title">245 ‡a</label>
                        <input type="text" name="title_statement" class="form-control"
                            value="{{ $book->title_statement }}" placeholder="Enter Title">
                    </div>

                    <div class="form-section">
                        <label for="title_responsibility">245 ‡c</label>
                        <input type="text" name="title_author" class="form-control" value="{{ $book->title_author }}"
                            placeholder="Enter Title Responsibility">
                    </div>

                    <div class="form-section">
                        <label for="edition">250</label>
                        <input type="text" name="edition" class="form-control" value="{{ $book->edition }}"
                            placeholder="Enter Edition">
                    </div>

                    <div class="form-section">
                        <label for="publication_place">264 ‡a</label>
                        <input type="text" name="pub_place" class="form-control" value="{{ $book->pub_place }}"
                            placeholder="Enter Publication Place">
                    </div>

                    <div class="form-section">
                        <label for="publisher">264 ‡b</label>
                        <input type="text" name="publisher" class="form-control" value="{{ $book->publisher }}"
                            placeholder="Enter Publisher">
                    </div>

                    <div class="form-section">
                        <label for="publication_year">264 ‡c</label>
                        <input type="text" name="pub_year" class="form-control" value="{{ $book->pub_year }}"
                            placeholder="Enter Publication Year">
                    </div>

                    <div class="form-section">
                        <label for="pages">300 ‡a</label>
                        <input type="text" name="pages" class="form-control" value="{{ $book->pages }}"
                            placeholder="Enter Pages">
                    </div>

                    <div class="form-section">
                        <label for="illustrations">300 ‡b</label>
                        <input type="text" name="illustrations" class="form-control" value="{{ $book->illustrations }}"
                            placeholder="Enter Illustrations">
                    </div>

                    <div class="form-section">
                        <label for="size">300 ‡c</label>
                        <input type="text" name="size" class="form-control" value="{{ $book->size }}"
                            placeholder="Enter Size">
                    </div>

                    <div class="form-section">
                        <label for="volume">300 ‡f</label>
                        <input type="text" name="volume" class="form-control" value="{{ $book->volume }}"
                            placeholder="Enter Volume">
                    </div>

                    <div class="form-section">
                        <label for="content_type">336 ‡a</label>
                        <input type="text" name="content_type" class="form-control" value="{{ $book->content_type }}"
                            placeholder="Enter Content Type">
                    </div>

                    <div class="form-section">
                        <label for="media_type">337 ‡a</label>
                        <input type="text" name="media_type" class="form-control" value="{{ $book->media_type }}"
                            placeholder="Enter Media Type">
                    </div>

                    <div class="form-section">
                        <label for="carrier_type">338 ‡a</label>
                        <input type="text" name="carrier_type" class="form-control" value="{{ $book->carrier_type }}"
                            placeholder="Enter Carrier Type">
                    </div>

                    <div class="form-section">
                        <label for="series_title">490 ‡a</label>
                        <input type="text" name="series_title" class="form-control" value="{{ $book->series_title }}"
                            placeholder="Enter Series Title">
                    </div>

                    <div class="form-section">
                        <label for="general_note">500 ‡a</label>
                        <textarea name="general_note" class="form-control" value="{{ $book->general_note }}"
                            placeholder="Enter General Note"></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-section">
                        <label for="bibliography_note">504</label>
                        <textarea name="bibliography_note" class="form-control" value="{{ $book->bibliography_note }}"
                            placeholder="Enter Bibliography Note"></textarea>
                    </div>

                    <div class="form-section">
                        <label for="acquisition_source">541</label>
                        <input type="text" name="source_vendor" class="form-control" value="{{ $book->source_vendor }}"
                            placeholder="Enter Acquisition Source">
                    </div>

                    <div class="form-section">
                        <label for="acquisition_date">541 ‡a</label>
                        <input type="date" name="source_date" class="form-control" value="{{ $book->source_date }}"
                            placeholder="Enter Acquisition Date">
                    </div>

                    <div class="form-section">
                        <label for="subject">650 ‡a</label>
                        <input type="text" name="subject_topic" class="form-control" value="{{ $book->subject_topic }}"
                            placeholder="Enter Subject">
                    </div>

                    <div class="form-section">
                        <label for="form">650 ‡v</label>
                        <input type="text" name="subject_form" class="form-control" value="{{ $book->subject_form }}"
                            placeholder="Enter Form">
                    </div>

                    <div class="form-section">
                        <label for="genre">655 ‡a</label>
                        <input type="text" name="genre" class="form-control" value="{{ $book->genre }}"
                            placeholder="Enter Genre">
                    </div>

                    <div class="form-section">
                        <label for="library_name">852 ‡b</label>
                        <input type="text" name="library_name" class="form-control" value="{{ $book->library_name }}"
                            placeholder="Enter Library Name">
                    </div>

                    <div class="form-section">
                        <label for="section">852 ‡c</label>
                        <input type="text" name="section" class="form-control" value="{{ $book->section }}"
                            placeholder="Enter Section">
                    </div>

                    <div class="form-section">
                        <label for="call_number">852 ‡h</label>
                        <input type="text" name="call_number" class="form-control" value="{{ $book->call_number }}"
                            placeholder="Enter Call Number">
                    </div>

                    <div class="form-section">
                        <label for="accession_no">949</label>
                        <input type="text" name="accession_no" class="form-control" value="{{ $book->accession_no }}"
                            placeholder="Enter Accession No.">
                    </div>

                    <div class="form-section">
                        <label for="barcode">876 ‡p</label>
                        <input type="text" name="barcode" class="form-control" value="{{ $book->barcode }}"
                            placeholder="Enter Barcode">
                    </div>

                    <div class="form-section">
                        <label for="rfid">RFID</label>
                        <input type="text" name="rfid" class="form-control" value="{{ $book->rfid }}"
                            placeholder="Enter RFID">
                    </div>

                    <div class="form-section">
                        <label for="year">996 ‡e</label>
                        <input type="text" name="year" class="form-control" value="{{ $book->year }}"
                            placeholder="Enter Year">
                    </div>

                    <div class="form-section">
                        <label for="course">650 ‡a</label>
                        <input type="text" name="course" class="form-control" value="{{ $book->course }}"
                            placeholder="Enter Course">
                    </div>

                    <div class="form-section">
                        <label>Program</label>
                        <div id="program-container">
                            @if($book->programs->isNotEmpty())
                            @foreach($book->programs as $program)
                            <div class="program-row d-flex mb-2">
                                <select name="program_ids[]" class="form-control">
                                    <option value="">-- Select Program --</option>
                                    @foreach($programs as $p)
                                    <option value="{{ $p->id }}" {{ $p->id == $program->id ? 'selected' : '' }}>
                                        {{ $p->program_name }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-danger btn-sm ms-2 remove-program-btn">X</button>
                            </div>
                            @endforeach
                            @else
                            <div class="program-row d-flex mb-2">
                                <select name="program_ids[]" class="form-control">
                                    <option value="">-- Select Program --</option>
                                    @foreach($programs as $p)
                                    <option value="{{ $p->id }}">{{ $p->program_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-danger btn-sm ms-2 remove-program-btn">X</button>
                            </div>
                            @endif
                        </div>
                        <button type="button" id="add-program-btn" class="btn btn-sm btn-secondary mt-1">Add More
                            Program</button>
                    </div>
                </div>


                <div class="form-section">
                    <label class="form-label">856:</label>
                    @if ($book->cover_image)
                    <div class="file-preview">

                        <img src="{{ asset('public/storage/' . $book->cover_image) }}" alt="Cover Image"
                            style="height: 280px; width: 380px; border-radius: 20px; display: block; margin-bottom: 10px;">

                    </div>
                    @endif
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('book.index') }}" class="btn btn-cancel">❌ Cancel</a>
                <button type="submit" class="btn btn-update">✅ Update Book</button>
            </div>
        </form>
    </div>



    @if ($errors->any())
    <div class="alert alert-danger mt-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("program-container");
            const addBtn = document.getElementById("add-program-btn");

            // Add new program dropdown
            addBtn.addEventListener("click", function () {
                let selectedValues = Array.from(container.querySelectorAll("select"))
                    .map(sel => sel.value)
                    .filter(v => v !== "");

                let wrapper = document.createElement("div");
                wrapper.classList.add("program-row", "d-flex", "mb-2");

                let select = document.createElement("select");
                select.name = "program_ids[]";
                select.classList.add("form-control");

                // Default option
                let defaultOpt = document.createElement("option");
                defaultOpt.value = "";
                defaultOpt.textContent = "-- Select Program --";
                select.appendChild(defaultOpt);

                // Populate options but exclude already selected
                @json($programs).forEach(p => {
                    if (!selectedValues.includes(p.id.toString())) {
                        let opt = document.createElement("option");
                        opt.value = p.id;
                        opt.textContent = p.program_name;
                        select.appendChild(opt);
                    }
                });

                let removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.textContent = "X";
                removeBtn.classList.add("btn", "btn-danger", "btn-sm", "ms-2", "remove-program-btn");
                removeBtn.addEventListener("click", () => wrapper.remove());

                wrapper.appendChild(select);
                wrapper.appendChild(removeBtn);
                container.appendChild(wrapper);
            });

            // Remove button for existing rows
            container.querySelectorAll(".remove-program-btn").forEach(btn => {
                btn.addEventListener("click", function () {
                    this.closest(".program-row").remove();
                });
            });
        });
    </script>

</body>

</html>