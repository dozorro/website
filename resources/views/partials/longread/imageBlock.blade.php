@if (!empty($data->image))
    <div class="c-bgimg" style="background-image: url('{{ \App\Helpers::getStoragePath($data->image->disk_name) }}');">
        <div class="c-bgimg__bgcolor c-bgimg__bgcolor--opacity-0{{ !empty($data->image_block_opacity) ? (9-$data->image_block_opacity) : 6 }}"></div>
        <div class="container">
            <div class="c-bgimg__table">
                <div class="c-bgimg__cell">
                    @if(!empty($data->image_block_title))
                        <h2>{{ $data->image_block_title }}</h2>
                    @endif
                    @if(!empty($data->image_block_text))
                        {!! $data->image_block_text !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif