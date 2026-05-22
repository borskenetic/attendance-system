<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container mt-4">
    <h2 class="mb-4">Add New Room</h2>

    <form action="{{ route('rooms.store') }}" method="POST" class="card p-4 shadow-sm bg-white">
        @csrf
        <div class="mb-3">
            <label class="form-label">Room Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save</button>
        </div>
    </form>
</div>
</body>
</html>
