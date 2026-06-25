<div class="data-panel-table-wrap">
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle patron-list-table">
            <thead>
                <tr>
                    <th scope="col">Profile</th>
                    <th scope="col">Student ID</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Course</th>
                    <th scope="col">Year</th>
                    <th scope="col">Actions</th>
                    <th scope="col">Generate ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            @if($student->profile_picture)
                                <img src="{{ patron_media_url($student->profile_picture) }}" alt="Profile" class="profile-img" loading="lazy" width="80" height="80">
                            @else
                                <span>No Image</span>
                            @endif
                        </td>
                        <td>{{ $student->student_id ?? '—' }}</td>
                        <td>{{ $student->lastname }}</td>
                        <td>{{ $student->firstname }}</td>
                        <td>{{ $student->course }}</td>
                        <td>{{ $student->year }}</td>
                        <td>
                            @can('isAdmin')
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Options</button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('students.edit', $student->id) }}">Edit</a></li>
                                        <li>
                                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete this student?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item" type="submit">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endcan
                        </td>
                        <td>
                            @can('isAdmin')
                                <div class="dropdown">
                                    <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">Generate</button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('idcard.front', $student->id) }}" target="_blank">Front</a></li>
                                        <li><a class="dropdown-item" href="{{ route('idcard.back', $student->id) }}" target="_blank">Back</a></li>
                                        <li><a class="dropdown-item" href="{{ route('idcard.download', $student->id) }}">Download ZIP</a></li>
                                    </ul>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3 data-panel-pagination">
        {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
