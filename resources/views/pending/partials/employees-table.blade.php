<div class="data-panel-table-wrap">
    <h5 class="mb-3" style="font-weight:700;">Pending Employees</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingEmployees as $e)
                    <tr>
                        <td>
                            @if($e->formal_picture)
                                <img src="{{ patron_media_url($e->formal_picture) }}" width="80" height="80" alt="" loading="lazy">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $e->firstname }} {{ $e->lastname }}</td>
                        <td>{{ $e->department }}</td>
                        <td>{{ $e->position }}</td>
                        <td>
                            <form action="{{ route('employees.approve', $e->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form action="{{ route('employees.reject', $e->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No pending employee registrations.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3 data-panel-pagination">
        {{ $pendingEmployees->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
