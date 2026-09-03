<div class="data-panel-table-wrap">
    <div class="table-responsive patron-table-scroll">
        <table class="table align-middle patron-list-table">
            <thead>
                <tr>
                    <th scope="col">Student</th>
                    <th scope="col">Student ID</th>
                    <th scope="col">Course</th>
                    <th scope="col">Year</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="patron-person">
                                @if($student->profile_picture)
                                    <img src="{{ patron_media_url($student->profile_picture) }}" alt="" class="patron-avatar" loading="lazy" width="40" height="40">
                                @else
                                    <span class="patron-avatar patron-avatar--empty" aria-hidden="true">{{ strtoupper(substr($student->firstname ?? '?', 0, 1)) }}</span>
                                @endif
                                <div class="patron-person-text">
                                    <div class="patron-person-name">{{ $student->lastname }}, {{ $student->firstname }}</div>
                                    @if($student->middle_initial)
                                        <div class="patron-person-meta">{{ $student->middle_initial }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="patron-mono">{{ $student->student_id ?? '—' }}</td>
                        <td>{{ $student->course ?: '—' }}</td>
                        <td>{{ $student->year ?: '—' }}</td>
                        <td class="text-end">
                            @can('isAdmin')
                                <div class="dropdown table-action-dropdown d-inline-block">
                                    <button class="btn btn-sm patron-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Actions for {{ $student->lastname }}">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('students.edit', $student->id) }}">Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('idcard.front', $student->id) }}?t={{ optional($student->updated_at)->timestamp ?? time() }}" target="_blank" data-turbo="false">ID front</a></li>
                                        <li><a class="dropdown-item" href="{{ route('idcard.back', $student->id) }}" target="_blank" data-turbo="false">ID back</a></li>
                                        <li><a class="dropdown-item" href="{{ route('idcard.download', $student->id) }}" data-turbo="false">Download ID ZIP</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete this student?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="patron-empty">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3 data-panel-pagination">
        {{ $students->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
