<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/css/books/show.css') }}">
</head>

<body>
    <div class="container-fluid px-3 px-md-5 mt-5">
        <h1 class="book-heading">
            <span class="text ms-2">Book Details</span>
        </h1>

        <div class="row g-4 align-items-start">
            <div class="col-12 col-md-4 text-center">
                @if ($book->cover_image)
                <p><strong>856 (Cover Image):</strong></p>
                <a href="{{ asset('public/storage/' . $book->cover_image) }}" target="_blank">
                    <img src="{{ asset('public/storage/' . $book->cover_image) }}" alt="Cover Image"
                        class="img-thumbnail" style="height: 4in; width: 3.5in;">
                </a>
                @else
                <span class="text-muted">No Image</span>
                @endif
            </div>

            <div class="col-12 col-md-8">
                <div style="display: inline-block;">
                    <h3 style="font-family: 'Rubik', sans-serif; font-weight: 700; margin-bottom: 5px;">{{ $book->title
                        }}</h3>
                    <div
                        style="height: 3px; width: 100%; background-color: #121e24; border-radius: 2px; margin-bottom: 30px;">
                    </div>
                </div>

                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th>001 (Control No.):</th>
                            <td>{{ $book->control_no }}</td>
                        </tr>
                        <tr>
                            <th>005 (Date & Time Stamp):</th>
                            <td>{{ $book->date_time_stamp }}</td>
                        </tr>
                        <tr>
                            <th>008 (Fixed-Length Data):</th>
                            <td>{{ $book->fixed_length_data }}</td>
                        </tr>
                        <tr>
                            <th>020 ‡a: (ISBN):</th>
                            <td>{{ $book->isbn }}</td>
                        </tr>
                        <tr>
                            <th>020 ‡c (Price):</th>
                            <td>{{ $book->price ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>040 ‡a (Cataloging Source):</th>
                            <td>{{ $book->cataloging_source_a ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>040 ‡b (Language):</th>
                            <td>{{ $book->cataloging_source_b ?? 'N/A' }}</td>
                        </tr>

                        {{-- Additional Fields --}}
                        <tr>
                            <th>040 ‡e (Description Conventions)</th>
                            <td>{{ $book->cataloging_source_e ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>100 ‡a (Main Author)</th>
                            <td>{{ $book->main_author ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>245 ‡a (Title)</th>
                            <td>{{ $book->title_statement ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>245 ‡c (Title Responsibility)</th>
                            <td>{{ $book->title_author ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>250 (Edition)</th>
                            <td>{{ $book->edition ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>264 ‡a (Publication Place)</th>
                            <td>{{ $book->pub_place ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>264 ‡b (Publisher)</th>
                            <td>{{ $book->publisher ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>264 ‡c (Publication Year)</th>
                            <td>{{ $book->pub_year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>300 ‡a (Pages)</th>
                            <td>{{ $book->pages ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>300 ‡b (Illustrations)</th>
                            <td>{{ $book->illustrations ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>300 ‡c(Size)</th>
                            <td>{{ $book->size ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>300 ‡f (Volume)</th>
                            <td>{{ $book->volume ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>336 ‡a (Content Type)</th>
                            <td>{{ $book->content_type ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>337 ‡a (Media Type)</th>
                            <td>{{ $book->media_type ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>338 ‡a (Carrier Type)</th>
                            <td>{{ $book->carrier_type ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>490 ‡a (Series Title)</th>
                            <td>{{ $book->series_title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>500 ‡a (General Note)</th>
                            <td>{{ $book->general_note ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>504 (Bibliography Note)</th>
                            <td>{{ $book->bibliography_note ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>541 (Acquisition Source)</th>
                            <td>{{ $book->source_vendor ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>541 ‡a (Acquisition Date)</th>
                            <td>{{ $book->source_date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>650 ‡a (Subject)</th>
                            <td>{{ $book->subject_topic ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>650 ‡v (Form)</th>
                            <td>{{ $book->subject_form ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>655 ‡a (Genre)</th>
                            <td>{{ $book->genre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>852 ‡b (Library Name)</th>
                            <td>{{ $book->library_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>852 ‡c (Call Number)</th>
                            <td>{{ $book->call_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>949 (Accession No.)</th>
                            <td>{{ $book->accession_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>876 ‡p (Barcode)</th>
                            <td>{{ $book->barcode ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>RFID</th>
                            <td>{{ $book->rfid ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>996 ‡e (Year)</th>
                            <td>{{ $book->year ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>650 ‡a (Course)</th>
                            <td>{{ $book->course ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>996 ‡f (Program)</th>
                            <td>
                                @if($book->programs && $book->programs->count() > 0)
                                @foreach($book->programs as $program)
                                <span class="badge bg-primary me-1">{{ $program->program_name }}</span>
                                @endforeach
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>


                        {{-- Status --}}
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($book->availability === 'Available')
                                <span class="text-success">Available</span>
                                @else
                                <span class="text-danger">Borrowed</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Last Borrower --}}
                        @php
                        $lastTransaction = $book->logs()->where('status', 'Checked Out')->latest()->first();
                        @endphp
                        @if($book->availability === 'Borrowed' && $lastTransaction)
                        <tr>
                            <th>Last Borrower:</th>
                            <td>{{ $lastTransaction->patron_name }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                {{-- Buttons --}}
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <a href="{{ route('book.index') }}" class="btn"
                        style="background-color: black; color: white; font-family: 'Rubik', sans-serif; font-weight: bold; transition: 0.3s;"
                        onmouseover="this.style.backgroundColor='#ffb845'; this.style.color='#22333b';"
                        onmouseout="this.style.backgroundColor='black'; this.style.color='white';">
                        ⬅ Back to List
                    </a>

                    @if($book->availability === 'Available')
                    <style>
                        .btn-hover-white:hover {
                            background-color: #3E5F44 !important;
                            color: white !important;
                        }
                    </style>
                    <a href="{{ route('logs.index', ['rfid' => $book->rfid, 'status' => 'checked_out']) }}"
                        class="btn btn-hover-white"
                        style="font-family: 'Rubik', sans-serif; font-weight: bold; color:white; background-color: #5E936C;">
                        Check Out
                    </a>
                    @else
                    <a href="{{ route('logs.index', [
              'rfid' => $book->rfid,
              'status' => 'checked_in',
              'patron_name' => $lastTransaction?->patron_name ?? ''
            ]) }}" class="btn btn-success">
                        🔄 Check In
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>

</html>