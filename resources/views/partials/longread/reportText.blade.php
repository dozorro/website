@if(!empty($data->title) || !empty($data->desc))
    <div class="bg_grey">
        <div class="container bg_white">
            <div class="page_list_text">
                @if(!empty($data->title1))
                <h3>{{ $data->title1 }}</h3>
                @endif
                @if(!empty($data->title2))
                <h4>{{ $data->title2 }}</h4>
                @endif
                @if(!empty($data->desc))
                <p>{!! $data->desc !!}</p>
                @endif
                @if(!empty($data->link))
                <p>
                    <a href="{{ $data->link }}" target="_blank">{{ $data->link }}</a>
                </p>
                @endif
                @if(!empty($data->iframe))
                <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ \App\Helpers::cut_str($data->iframe, 'v=', '&') }}" frameborder="0" allowfullscreen></iframe>
                @endif
            </div>
        </div>
    </div>
@endif