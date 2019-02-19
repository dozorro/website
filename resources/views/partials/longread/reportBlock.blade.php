@if(!empty($data->items))
    <div class="bg_grey ">
        <div class="container ">
            <div class="list_project ">
                @if(isset($data->title))
                    <h4>{{ $data->title }}</h4>
                @endif
                @if(isset($data->desc))
                    <div class="block_desc">
                        <p>{{ $data->desc }}</p>
                    </div>
                @endif
                <div class="inline-layout">

                    @foreach($data->items as $item)
                        <div class="item">
                            <div class="item__wrap">
                                <div class="image-holder">
                                    @if($item->slug)
                                        <a href="{{ route('page.report', ['slug' => $item->slug]) }}" target="_blank">
                                    @endif
                                        <div class="img" style="background-image: url({{ \App\Helpers::getMediaPath($item->image) }})"></div>
                                    @if($item->slug)
                                        </a>
                                    @endif
                                </div>
                                <div class="block_info">
                                    <h4 title="{{ $item->title }}">
                                        @if($item->slug)
                                            <a href="{{ route('page.report', ['slug' => $item->slug]) }}" target="_blank">
                                        @endif
                                            {{ $item->title }}
                                        @if($item->slug)
                                            </a>
                                        @endif

                                    </h4>
                                    <div class="block_text">
                                        <p>
                                            {!! $item->desc !!}
                                        </p>
                                    </div>
                                    @if($item->slug)
                                        <a target="_blank" href="{{ route('page.report', ['slug' => $item->slug]) }}" class="more_link">{{ t('page.show_inner') }}</a>
                                    @endif
                                    <div class="list_link_project">
                                        @if($item->link1)
                                            <a href="{{ $item->link1 }}" target="_blank">{{ $item->link1 }}</a>
                                        @endif
                                        @if($item->link2)
                                            <a href="{{ $item->link2 }}" target="_blank">{{ $item->link2 }}</a>
                                        @endif
                                        @if($item->author)
                                            <strong @if($item->is_author_color){{'style=color:#e55166;'}}@endif>{{ $item->author }}</strong>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
