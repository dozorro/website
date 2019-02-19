@if(!empty($data->page_header_title) || !empty($data->page_header_text))
    <div class="block_img_oskardjennia">
        <div class="container inline-layout">
            <div class="block_text">
                @if(!empty($data->page_header_title))
                    <h2>{{ $data->page_header_title }}</h2>
                @endif
                @if(!empty($data->page_header_text))
                    {!! $data->page_header_text !!}
                @endif
            </div>
            @if(!empty($data->page_header_image))
                <div class="img-holder">
                    <img src="{{ \App\Helpers::getStoragePath($data->page_header_image->disk_name) }}" alt="{{ $data->page_header_title }}">
                </div>
            @endif
        </div>
    </div>
@endif