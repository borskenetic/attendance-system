@php
    $items = $items ?? [];
@endphp

@if (count($items) > 0)
    <nav class="page-breadcrumb" aria-label="Breadcrumb">
        <ol class="page-breadcrumb__list">
            @foreach ($items as $item)
                <li @class([
                    'page-breadcrumb__item',
                    'page-breadcrumb__item--current' => $item['current'] ?? false,
                ])>
                    @if (! ($item['current'] ?? false) && ! empty($item['url']))
                        <a href="{{ $item['url'] }}" class="page-breadcrumb__link">
                            @if ($loop->first)
                                <i class="bi bi-house-door page-breadcrumb__home-icon" aria-hidden="true"></i>
                            @endif
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @else
                        <span @if($item['current'] ?? false) aria-current="page" @endif>
                            @if ($loop->first && ($item['current'] ?? false))
                                <i class="bi bi-house-door page-breadcrumb__home-icon" aria-hidden="true"></i>
                            @endif
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
