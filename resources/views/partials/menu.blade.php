@if(!empty($menu))
    <ul @if ($depth == 0) class="c-header__nav-list menu-header" @else class="drop-menu" @endif>

        @foreach($menu as $item)
            <li @if ($item->nest_depth == 0 && sizeof($item->children()) > 0) class="parent" @endif>
                <a @if(!$item->is_not_page)href="{{ $item->href }}"@endif @if($item->active) class="is-active"@endif @if($item->is_target_blank) target="_blank"@endif>{{ $item->title }}</a>
                @if (sizeof($item->children()) > 0)
                    @include('partials.menu', [
                        'menu' => $item->children(),
                        'depth' => ($depth + 1),
                    ])
                @endif
            </li>
        @endforeach
        @if ($depth == 0)
            @include('partials.lang', [
                'locales' => $locales,
            ])
        @endif
    </ul>
@endif