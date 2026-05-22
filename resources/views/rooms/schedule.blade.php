<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
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
        .past-reservation {
            background-color: #f8d7da !important;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="page-header">
        <h2 class="mb-0">Manage Room Reservations</h2>
        <a href="{{ auth()->check() ? route('books.index') : route('home') }}" 
           class="btn btn-outline-secondary btn-sm">
           🏠 Back to Home
        </a>
    </div>

    <a href="{{ route('rooms.pending') }}" class="btn btn-warning mb-3">
        View Pending Requests
    </a>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if ($reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $res)
                                @continue($res->status === 'rejected') {{-- Skip rejected reservations --}}
                                
                                @php
                                    $isPast = false;
                                    if ($res->start_time && $res->date) {
                                        try {
                                            $start = \Carbon\Carbon::parse($res->date.' '.$res->start_time);
                                            $isPast = $start->isPast();
                                        } catch (Exception $e) {
                                            $isPast = false;
                                        }
                                    }
                                @endphp
                            
                                <tr class="{{ $isPast ? 'past-reservation' : '' }}">
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
                                    <td>
                                        @if($res->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($res->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($res->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('rooms.show', $res->id) }}" 
                                           class="btn btn-sm btn-info px-3">
                                           View
                                        </a>
                                        <form action="{{ route('resrooms.destroy', $res->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to remove this reservation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger px-3">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted mb-0">No room reservations found.</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
