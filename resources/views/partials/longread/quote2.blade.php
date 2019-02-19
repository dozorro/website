@if(!empty($data->text_text))
    <div class="c-text">
        <div class="container">
            @if(!empty($data->author))
                <h2>{{ $data->author }}</h2>
            @endif
            @if(!empty($data->position))
                <h3>{{ $data->position }}</h3>
            @endif
            {!! $data->text_text !!}
            @if (!empty($data->image))
                <figure>
                    <img src="{{ \App\Helpers::getStoragePath($data->image->disk_name) }}" width="100%">
                </figure>
            @endif
        </div>
    </div>
@endif