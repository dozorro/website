@if(!empty($data->file_buttons_files))
    <div class="block_zakon">
        <div class="container">
            <div class="list_templates inline-layout">
                @foreach($data->file_buttons_files as $item)
                    <div class="item">
                        <div class="item-wrap inline-layout">
                            <div class="block_text">
                                @if(!empty($item->title))
                                    <div class="template_name">
                                        {{ $item->title }}
                                    </div>
                                @endif
                                @if(!empty($item->button_text))
                                    <a class="link_template" href="{{ !empty($item->button_href) ? $item->button_href : '' }}" target="_blank">{{ $item->button_text }}</a>
                                @endif
                            </div>
                            <div class="img-holder">
                                <img src="/assets/images/template_icon.png" alt="name">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif