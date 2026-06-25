<div class="data-panel-table-wrap">
    <h5 class="mb-3" style="font-weight:700;">Pending Students</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>ID Number</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingStudents as $p)
                    <tr>
                        <td>
                            @if($p->profile_picture)
                                <img src="{{ patron_media_url($p->profile_picture) }}" width="80" height="80" alt="" loading="lazy">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $p->student_id ?? '—' }}</td>
                        <td>{{ $p->firstname }} {{ $p->lastname }}</td>
                        <td>{{ $p->course }}</td>
                        <td>{{ $p->year }}</td>
                        <td>
                            <form action="{{ route('students.approve', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form action="{{ route('students.reject', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No pending student registrations.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3 data-panel-pagination">
        {{ $pendingStudents->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
