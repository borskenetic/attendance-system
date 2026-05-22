<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Rooms</h2>
        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">🏠 Back to Home</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('rooms.create') }}" class="btn btn-primary mb-3">+ Add New Room</a>

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Capacity</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($rooms as $room)
            <tr>
                <td>{{ $room->name }}</td>
                <td>{{ $room->description ?? '—' }}</td>
                <td>{{ $room->capacity }}</td>
                <td>
                    <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted">No rooms yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
