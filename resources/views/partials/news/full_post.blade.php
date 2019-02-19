@if($post)
    <div class="sb-new-header" style="background-color: #e55166;">
        <div class="sb-new-header__bg" @if(!$post->is_badge)style="background-image: url('{{ $post->photo() }}');"@endif></div>
        <div class="sb-new-header__content" @if($post->is_badge)style="height: auto;"@endif>
            <div class="sb-new-header__row">
                <div class="sb-new-header__author-wrap">
                    <?php $author_photo = $post->author->photo(); ?>
                    @if($author_photo)
                        <div class="sb-new-header__author-img" style="background-image: url('{{ $post->author->photo() }}');"></div>
                    @endif
                    <h3>{{ $post->getAuthor()->full_name }}</h3>
                </div>
            </div>
            <div class="sb-new-header__row">
                <h2>{{ $post->title }}</h2>
            </div>
            <div class="sb-new-header__row sb-new-header__row--date-comments">
                <div class="sb-new-header__date">@datetime($post->created_at)</div>
                {{--<a href="#" class="sb-new-header__comments">0</a>--}}
            </div>
            @if(!$post->getTags()->isEmpty())
            <div class="sb-new-header__row sb-new-header__row--tags">
                <ul class="sb-new-header__tags-list">
                    @foreach($post->getTags() as $tag)
                        <li><a href="{{ route('page.blog.by_tag', ['slug' => $tag->slug ]) }}">{{ $tag->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{--
            <div class="sb-new-header__row sb-new-header__row--tender-num">
                <div class="sb-new-header__tender-num">Номер тендеру: 1233552  |  2244222</div>
            </div>
            --}}
        </div>
    </div>
@endif