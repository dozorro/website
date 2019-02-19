@if(!empty($data->pink_button_text))
    <div class="block_text_oskardjennia">
        <div class="container">
            <div class="block_text">
                <p><a class="link_letter" href="{{ !empty($data->pink_button_href) ? $data->pink_button_href : '' }}"{{ !empty($data->pink_button_is_target_blank) ? ' target="_blank"' : '' }}>{{ $data->pink_button_text }}</a></p>
            </div>
        </div>
    </div>
@endif