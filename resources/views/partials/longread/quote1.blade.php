@if(!empty($data->text_text))
    <div class="c-text">
        <div class="container">
            @if(!empty($data->author))
                <h2>{{ $data->author }}</h2>
            @endif
            {!! $data->text_text !!}
        </div>
    </div>
@endif