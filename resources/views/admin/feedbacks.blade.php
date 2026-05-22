@extends('layouts.sec')

@section('title', 'Attendance Feedback Report')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold mb-0">Attendance Feedback Report</h2>
    </div>

    @php $active = request('rating'); @endphp

    {{-- TOTAL (Clickable Reset) --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('feedback.index') }}" class="text-decoration-none">
                <div class="card text-center shadow-sm bg-dark text-white {{ !$active ? 'border-3 border-light' : '' }}">
                    <div class="card-body">
                        <h6>Total Responses</h6>
                        <h3>{{ $total }}</h3>
                        <small>Click to reset filter</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- RATING CARDS --}}
    <div class="row g-3 mb-4">

        <div class="col-md-2">
            <a href="{{ route('feedback.index', ['rating' => 'excellent']) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-success {{ $active == 'excellent' ? 'border-3' : '' }}">
                    <div class="card-body text-success">
                        <h6>Excellent</h6>
                        <h3>{{ $excellent }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-2">
            <a href="{{ route('feedback.index', ['rating' => 'good']) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-primary {{ $active == 'good' ? 'border-3' : '' }}">
                    <div class="card-body text-primary">
                        <h6>Good</h6>
                        <h3>{{ $good }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-2">
            <a href="{{ route('feedback.index', ['rating' => 'medium']) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-warning {{ $active == 'medium' ? 'border-3' : '' }}">
                    <div class="card-body text-warning">
                        <h6>Medium</h6>
                        <h3>{{ $medium }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-2">
            <a href="{{ route('feedback.index', ['rating' => 'poor']) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-danger {{ $active == 'poor' ? 'border-3' : '' }}">
                    <div class="card-body text-danger">
                        <h6>Poor</h6>
                        <h3>{{ $poor }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-2">
            <a href="{{ route('feedback.index', ['rating' => 'very_bad']) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-dark {{ $active == 'very_bad' ? 'border-3' : '' }}">
                    <div class="card-body text-dark">
                        <h6>Very Bad</h6>
                        <h3>{{ $veryBad }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-2">
            <a href="{{ route('feedback.index', ['rating' => 'declined']) }}" class="text-decoration-none">
                <div class="card text-center shadow-sm border-secondary {{ $active == 'declined' ? 'border-3' : '' }}">
                    <div class="card-body text-secondary">
                        <h6>Declined</h6>
                        <h3>{{ $declined }}</h3>
                    </div>
                </div>
            </a>
        </div>

    </div>


    {{-- DISTRIBUTION BAR (RESTORED) --}}
    @if($total > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Overall Distribution</h5>

            <div class="progress" style="height: 28px;">

                <div class="progress-bar bg-success"
                     style="width: {{ ($excellent/$total)*100 }}%"
                     title="Excellent"></div>

                <div class="progress-bar bg-primary"
                     style="width: {{ ($good/$total)*100 }}%"
                     title="Good"></div>

                <div class="progress-bar bg-warning"
                     style="width: {{ ($medium/$total)*100 }}%"
                     title="Medium"></div>

                <div class="progress-bar bg-danger"
                     style="width: {{ ($poor/$total)*100 }}%"
                     title="Poor"></div>

                <div class="progress-bar bg-dark"
                     style="width: {{ ($veryBad/$total)*100 }}%"
                     title="Very Bad"></div>

                <div class="progress-bar bg-secondary"
                     style="width: {{ ($declined/$total)*100 }}%"
                     title="Declined"></div>

            </div>
        </div>
    </div>
    @endif


    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">
                {{ $active ? strtoupper(str_replace('_',' ', $active)) . " Responses" : "All Responses" }}
            </h5>

            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Rating</th>
                        <th>Declined</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($feedbacks as $index => $feedback)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                {{ optional($feedback->student)->lastname ?? '' }},
                                {{ optional($feedback->student)->firstname ?? '' }}
                            </td>

                            <td>{{ $feedback->rating ?? '-' }}</td>

                            <td>{{ $feedback->declined ? 'Yes' : 'No' }}</td>

                            <td>{{ $feedback->created_at?->format('M d, Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No feedback found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>
@endsection