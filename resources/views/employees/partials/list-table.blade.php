<div class="data-panel-table-wrap">
    <div class="table-responsive patron-table-scroll">
        <table class="table align-middle patron-list-table">
            <thead>
                <tr>
                    <th scope="col">Employee</th>
                    <th scope="col">Employee ID</th>
                    <th scope="col">Department</th>
                    <th scope="col">Position</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faculty as $employee)
                    <tr>
                        <td>
                            <div class="patron-person">
                                @if($employee->formal_picture)
                                    <img src="{{ patron_media_url($employee->formal_picture) }}" alt="" class="patron-avatar" loading="lazy" width="40" height="40">
                                @else
                                    <span class="patron-avatar patron-avatar--empty" aria-hidden="true">{{ strtoupper(substr($employee->firstname ?? '?', 0, 1)) }}</span>
                                @endif
                                <div class="patron-person-text">
                                    <div class="patron-person-name">{{ $employee->lastname }}, {{ $employee->firstname }}</div>
                                    @if($employee->middle_name)
                                        <div class="patron-person-meta">{{ $employee->middle_name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="patron-mono">{{ $employee->employee_id ?? $employee->qrcode ?? '—' }}</td>
                        <td>{{ $employee->department ?: '—' }}</td>
                        <td>{{ $employee->position ?: '—' }}</td>
                        <td class="text-end">
                            <div class="dropdown table-action-dropdown d-inline-block">
                                <button class="btn btn-sm patron-action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Actions for {{ $employee->lastname }}">
                                    Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('employees.edit', $employee->id) }}">Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('employees.idcard.front', $employee->id) }}" target="_blank" data-turbo="false">ID front</a></li>
                                    <li><a class="dropdown-item" href="{{ route('employees.idcard.back', $employee->id) }}" target="_blank" data-turbo="false">ID back</a></li>
                                    <li><a class="dropdown-item" href="{{ route('employees.idcard.download', $employee->id) }}" data-turbo="false">Download ID ZIP</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="patron-empty">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3 data-panel-pagination">
        {{ $faculty->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
