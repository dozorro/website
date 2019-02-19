@if(!empty($menu))
    @foreach($menu as $item)
    <div class="col-md-4">
        <div class="sb-footer-card">
            <h3>{{ $item->title }}</h3>
            @if (sizeof($item->children()) > 0)
                <ul>
                @foreach($item->children() as $_item)
                    <li><a href="{{ $_item->href }}" @if($_item->is_target_blank) target="_blank"@endif>{{ $_item->title }}</a></li>
                @endforeach
                </ul>
            @endif
        </div>
    </div>
    @endforeach
@endif
