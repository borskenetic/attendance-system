<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Reservation Logs</title>
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
        .badge {
            font-size: 0.85rem;
        }
        table {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="page-header">
        <h2 class="mb-0">Room Reservation Logs</h2>
        <a href="{{ route('rooms.schedule') }}" class="btn btn-outline-secondary btn-sm">
            ⬅ Back to Schedule
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                                <th>Performed By</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->reservation->room->name ?? 'N/A' }}</td>
                                    <td>{{ $log->reservation->date ?? 'N/A' }}</td>
                                    <td>
                                        @if($log->reservation?->start_time && $log->reservation?->end_time)
                                            {{ \Carbon\Carbon::parse($log->reservation->start_time)->format('g:i A') }}
                                            –
                                            {{ \Carbon\Carbon::parse($log->reservation->end_time)->format('g:i A') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @switch($log->action)
                                            @case('approved')
                                                <span class="badge bg-success">Approved</span>
                                                @break
                                            @case('created')
                                                <span class="badge bg-primary">Created</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $log->user->name ?? 'System' }}</td>
                                    <td>{{ $log->created_at->format('Y-m-d h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->links() }}
                </div>
            @else
                <p class="text-center text-muted mb-0">No logs found.</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>
