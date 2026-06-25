<div class="data-panel-table-wrap">
    <div class="al-table-wrap">
        <table class="al-table">
            <thead>
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Status</th>
                    <th>Scanned At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->student ? $log->student->lastname : 'Unknown' }}</td>
                        <td>{{ $log->student ? $log->student->firstname : 'Unknown' }}</td>
                        <td>{{ $log->student ? $log->student->course : 'Unknown' }}</td>
                        <td>{{ $log->section ?? '—' }}</td>
                        <td>
                            @php $status = strtolower($log->status); @endphp
                            @if($status === 'in')
                                <span class="in">IN</span>
                            @elseif($status === 'out')
                                <span class="out">OUT</span>
                            @else
                                <span class="out" style="background:#6b7280;">Unknown</span>
                            @endif
                        </td>
                        <td>
                            {{ $log->scanned_at?->format('Y-m-d h:i A') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="al-empty">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4 data-panel-pagination">
        {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
