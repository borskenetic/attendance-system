@props([
    'registerRoute' => null,
    'registerLabel' => 'Register',
    'pendingUrl' => '#',
    'importTemplateRoute',
    'importRoute',
    'exportRoute',
    'downloadIdsRoute',
])

<div class="patron-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-2 border bg-white">
    <div class="patron-toolbar-primary d-flex flex-wrap align-items-center gap-2">
        @if($registerRoute)
            <a href="{{ $registerRoute }}" class="btn btn-add btn-sm">{{ $registerLabel }}</a>
        @endif
        <a href="{{ $pendingUrl }}" class="btn btn-sm btn-outline-secondary patron-toolbar-link">Pending</a>
    </div>

    <div class="patron-toolbar-secondary d-flex flex-wrap align-items-center gap-2">
        @can('isAdmin')
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Import
                </button>
                <div class="dropdown-menu dropdown-menu-end patron-toolbar-menu p-3">
                    <a href="{{ route($importTemplateRoute) }}" class="dropdown-item px-0 mb-2" data-turbo="false">Download template</a>
                    <form action="{{ route($importRoute) }}" method="POST" enctype="multipart/form-data" class="patron-import-form" data-turbo="false">
                        @csrf
                        <label class="patron-import-file w-100 mb-2">
                            <span class="btn btn-light btn-sm w-100 mb-0">Choose file</span>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Import</button>
                    </form>
                </div>
            </div>
        @endcan

        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ $exportRoute }}" data-turbo="false">Export spreadsheet</a></li>
                <li><a class="dropdown-item" href="{{ $downloadIdsRoute }}" data-turbo="false">Download IDs</a></li>
            </ul>
        </div>
    </div>
</div>
