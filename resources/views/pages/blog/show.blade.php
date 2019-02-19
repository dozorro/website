@extends('layouts/app')

@section('head')
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $post->clear_title() }}" />
    <meta property="og:image" content="{{ $post->photo() }}" />
    @foreach($blocks as $block)
        @if($block->alias=='text')
            <meta property="og:description" content="{{ $post->clear_short_description($block->value->text_text) }}" />
            <?php break; ?>
        @endif
    @endforeach

@endsection

@section('content')
    <div class="c-blog">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="c-blog__left">
                        <div class="c-one-new">

                            @include('partials.blog.full_post')

                            <div class="c-one-new__content">
                                @foreach($blocks as $block)
                                    @include('partials.longread.' . $block->alias, [
                                        'data' => $block->value
                                    ])
                                @endforeach
                            </div>

                        </div>

                        <div class="likely" data-title="{{ $post->title }}" data-url="{{ route('page.blog.post', ['slug' => $post->slug]) }}">
                            <div class="facebook">Share</div>
                            <div class="twitter">Tweet</div>
                        </div>

                    </div>
                    <div id="disqus_thread"></div>
                </div>

                @include('partials.blog.sidebar', ['latest_posts' => $latest_posts])

            </div>
        </div>
    </div>

    {{-- @push('scripts')
    <script type="text/javascript">
        var disqus_config = function () {
            this.page.url = '{{ route('page.blog.post', ['slug' => $post->slug]) }}';
            this.page.identifier = '{{ $post->id }}';
        };

        (function() { // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');
            s.src = '//dozorro.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        })();
    </script>
    <script id="dsq-count-scr" src="//dozorro.disqus.com/count.js" async type="text/javascript"></script>
    @endpush
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    --}}

@endsection