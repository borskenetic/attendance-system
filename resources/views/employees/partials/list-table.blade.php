<div class="data-panel-table-wrap">
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle patron-list-table">
            <thead>
                <tr>
                    <th scope="col">Profile</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Department</th>
                    <th scope="col">Position</th>
                    <th scope="col">Employee ID</th>
                    <th scope="col">Actions</th>
                    <th scope="col">Generate ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faculty as $employee)
                    <tr>
                        <td>
                            @if($employee->formal_picture)
                                <img src="{{ patron_media_url($employee->formal_picture) }}" width="80" height="80" class="rounded" alt="" loading="lazy">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $employee->lastname }}</td>
                        <td>{{ $employee->firstname }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->employee_id ?? $employee->qrcode }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Options</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('employees.edit', $employee->id) }}">Edit</a></li>
                                    <li>
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item" type="submit">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Generate</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('employees.idcard.front', $employee->id) }}" target="_blank">Front</a></li>
                                    <li><a class="dropdown-item" href="{{ route('employees.idcard.back', $employee->id) }}" target="_blank">Back</a></li>
                                    <li><a class="dropdown-item" href="{{ route('employees.idcard.download', $employee->id) }}">Download ZIP</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">No employees found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3 data-panel-pagination">
        {{ $faculty->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
