<!--
@if(sizeof($locales) > 1)
    <li class="lang">
        <ul style="padding: 0;">
            @foreach($locales as $language)
                <li class="{{$language->is_current==$language->code ? 'active':''}}">
                    <a href="{{ $language->href }}">{{ $language->name }}</a>
                </li>
            @endforeach
        </ul>
    </li>
@endif
-->