@if (!empty($data->image))
    
            @if(!empty($data->image_title))
                <h3>{{ $data->image_title }}</h3>
            @endif
            <figure>
                <img src="{{ \App\Helpers::getStoragePath($data->image->disk_name) }}" width="100%">
                @if(!empty($data->image_text))
                    <figcaption><p>{{ $data->image_text }}</p></figcaption>
                @endif
            </figure>
        
@endif