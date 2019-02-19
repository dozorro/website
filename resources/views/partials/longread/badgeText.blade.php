@if(isset($post) && $post->is_badge && (!empty($data->text_title) || !empty($data->text_text)))
    <div class="c-text{{!empty($data->is_text_grey) ? ' bg-grey':'' }}">
        <div class="container">
            <div style="text-align: left;">
            @if(!empty($data->text_title))
                <h2 style="display: inline-block;float: left;">{{ $data->text_title }}</h2>
            @endif
            <div style="float: left;margin-left: 20px;"><img style="margin-top: 0;" alt="{{ $data->text_title }}" src="{{ $post->photo() }}"></div>
            </div>
            <div style="clear: both;"></div>
            {!! $data->text_text !!}
        </div>
    </div>
    <div style="clear: both;"></div>
@endif