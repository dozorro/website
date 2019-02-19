@if(!empty($data->items))
    <div class="bg_grey p-b-30">
        <div class="container bg_white">
            <div class="block_faq">
                @if($data->title)
                    <h4>{{ $data->title }}</h4>
                @endif
                @foreach($data->items as $item)
                    <div class="item">
                        @if($item->title)
                            <h5>{{ $item->title }}</h5>
                        @endif
                        <div class="faq_text">
                            <p>
                                {!! $item->desc !!}
                            </p>
                            @if($item->image)
                            <div class="img-holder">
                                <img src="{{ \App\Helpers::getMediaPath($item->image) }}" alt="">
                            </div>
                            @endif
                            @if(isset($item->link))
                            <p>
                                <a target="_blank" href="{{ $item->link }}">@if($item->link_name){{ $item->link_name }}@else{{$item->link}}@endif</a>
                            </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif