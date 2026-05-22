<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0d6efd;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 0.4em 0.75em;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Reservation Details</h2>
        <a href="{{ route('rooms.schedule') }}" class="btn btn-outline-secondary btn-sm">⬅ Back to Schedule</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $reservation->room->name ?? 'N/A' }}</h5>

            <div class="row mb-2">
                <div class="col-md-6 mb-2">
                    <span class="detail-label">📅 Date:</span>
                    <div>{{ $reservation->date ?? 'N/A' }}</div>
                </div>

                <div class="col-md-6 mb-2">
                    <span class="detail-label">⏰ Time:</span>
                    <div>
                        @if($reservation->start_time && $reservation->end_time)
                            {{ \Carbon\Carbon::parse($reservation->start_time)->format('g:i A') }}
                            – 
                            {{ \Carbon\Carbon::parse($reservation->end_time)->format('g:i A') }}
                        @else
                            N/A
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6 mb-2">
                    <span class="detail-label">📧 Patron Email:</span>
                    <div>{{ $reservation->patron_email ?? 'N/A' }}</div>
                </div>

                <div class="col-md-6 mb-2">
                    <span class="detail-label">👥 Number of Students:</span>
                    <div>{{ $reservation->number_of_students ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="mb-3">
                <span class="detail-label">📝 Status:</span><br>
                @if($reservation->status == 'approved')
                    <span class="badge bg-success badge-status">Approved</span>
                @elseif($reservation->status == 'pending')
                    <span class="badge bg-warning text-dark badge-status">Pending</span>
                @else
                    <span class="badge bg-secondary badge-status">{{ ucfirst($reservation->status ?? 'N/A') }}</span>
                @endif
            </div>

            <div>
                <span class="detail-label">👩‍🎓 Student Names:</span>
                @if($reservation->students->isNotEmpty())
                    <ul class="mt-2">
                        @foreach($reservation->students as $s)
                            <li>{{ $s->name }}</li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted">No student records found.</div>
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>
