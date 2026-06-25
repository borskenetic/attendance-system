@php
    $columns = $columns ?? 8;
    $rows = $rows ?? 8;
    $headers = $headers ?? [];
    $loadingLabel = $loadingLabel ?? 'Loading…';
    $tableClass = $tableClass ?? 'table table-bordered text-center align-middle patron-list-table';
    $wrapClass = $wrapClass ?? 'data-panel-table-wrap data-panel-table-wrap--loading';
@endphp

<div class="{{ $wrapClass }}" aria-busy="true" aria-live="polite">
    <span class="visually-hidden">{{ $loadingLabel }}</span>
    <div class="table-responsive">
        <table class="{{ $tableClass }}">
            @if (count($headers) > 0)
                <thead>
                    <tr>
                        @foreach ($headers as $header)
                            <th scope="col">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="skeleton-table__body">
                @for ($i = 0; $i < $rows; $i++)
                    <tr class="skeleton-table__row">
                        @for ($c = 0; $c < $columns; $c++)
                            <td>
                                @if ($c === 0 && ($skeletonFirstCol ?? 'avatar') === 'avatar')
                                    <span class="skeleton-block skeleton-block--avatar placeholder-glow"></span>
                                @elseif ($c === 0 && ($skeletonFirstCol ?? 'avatar') === 'text')
                                    <span class="skeleton-block skeleton-block--md placeholder-glow"></span>
                                @elseif ($c === $columns - 2 || $c === $columns - 1)
                                    <span class="skeleton-block skeleton-block--btn placeholder-glow"></span>
                                @else
                                    <span class="skeleton-block skeleton-block--md placeholder-glow"></span>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center gap-2 mt-3 data-panel-pagination skeleton-pagination placeholder-glow" aria-hidden="true">
        <span class="skeleton-block skeleton-block--page"></span>
        <span class="skeleton-block skeleton-block--page"></span>
        <span class="skeleton-block skeleton-block--page skeleton-block--page-active"></span>
        <span class="skeleton-block skeleton-block--page"></span>
        <span class="skeleton-block skeleton-block--page"></span>
    </div>
</div>
