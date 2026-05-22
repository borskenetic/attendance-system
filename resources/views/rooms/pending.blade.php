<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .students-list {
            margin: 0;
            padding-left: 1.2rem;
        }
        .btn-action {
            display: inline-block;
            margin: 2px;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="page-header">
        <h2 class="mb-0">Manage Room Reservations</h2>
        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">
            🏠 Back to Home
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($pending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patron Email</th>
                                <th>Students</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending as $res)
                                <tr>
                                    <td>{{ $res->room->name ?? 'N/A' }}</td>
                                    <td>{{ $res->date ?? 'N/A' }}</td>
                                    <td>
                                        @if($res->start_time && $res->end_time)
                                            {{ \Carbon\Carbon::parse($res->start_time)->format('g:i A') }}
                                            –
                                            {{ \Carbon\Carbon::parse($res->end_time)->format('g:i A') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $res->patron_email ?? 'N/A' }}</td>
                                    <td>
                                        @if($res->students && count($res->students) > 0)
                                            <ul class="students-list">
                                                @foreach($res->students as $s)
                                                    <li>{{ $s->name }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">No students listed</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <form action="{{ route('rooms.approve', $res->id) }}" method="POST" class="btn-action">
                                                @csrf
                                                <button class="btn btn-success btn-sm px-3">
                                                    Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('rooms.reject', $res->id) }}" method="POST" class="btn-action">
                                                @csrf
                                                <button class="btn btn-danger btn-sm px-3"
                                                        onclick="return confirm('Are you sure you want to reject this reservation?')">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted mb-0">No pending reservations found.</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
