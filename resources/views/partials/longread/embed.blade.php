<div class="container">
@if(!empty($data->embed_code))
    @if(!empty($data->embed_title))
        <h3>{{ $data->embed_title }}</h3>
    @endif

    {!! $data->embed_code !!}
@endif
</div>