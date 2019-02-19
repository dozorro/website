@if(!empty($data->title) || !empty($data->desc))
    <div class="block_text_gradient ">
        <div class="container text-center">
            @if(!empty($data->title))
                <h3>{{ $data->title }}</h3>
            @endif
            <div class="block_text ">
                <p>{!! $data->desc !!}</p>
            </div>
        </div>
    </div>
@endif