<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Copies</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('public/css/books/index.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS (needed for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<div class="container mt-4">

    <!-- Back Button -->
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">← Back</a>

    <h3>
        Copies of: <strong>{{ $title }}</strong><br>
        <small>{{ $author }} — {{ $year }}</small>
    </h3>

    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>Accession No</th>
                <th>Barcode</th>
                <th>RFID</th>
                <th>Availability</th>
                <th>Date Added</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($copies as $copy)
            <tr>
                <td>{{ $copy->accession_no }}</td>
                <td>{{ $copy->barcode }}</td>
                <td>{{ $copy->rfid }}</td>

                <td class="{{ $copy->availability === 'Available' ? 'text-success' : 'text-danger' }}">
                    {{ $copy->availability }}
                </td>

                <td>{{ $copy->created_at?->format('Y-m-d') }}</td>

                <td>
                    <div class="dropdown1">
                        <button class="dropdown1-button">Actions</button>
                        <div class="dropdown1-content">
                            <a href="{{ route('book.show', $copy->id) }}" class="dropdown-item1">View</a>
                            <a href="{{ route('book.edit', $copy->id) }}" class="dropdown-item2">Edit</a>

                            <button class="dropdown-item3" 
                                type="button" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal{{ $copy->id }}">
                                Delete
                            </button>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $copy->id }}" tabindex="-1"
                        aria-labelledby="deleteModalLabel{{ $copy->id }}" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-3 shadow-lg">

                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Confirm Delete</h5>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    Are you sure you want to delete
                                    <strong>{{ $copy->title_statement }}</strong> (copy)?
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>

                                    <form action="{{ route('books.destroy', $copy->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $copies->links('pagination::bootstrap-5') }}

</div>

</body>
</html>
