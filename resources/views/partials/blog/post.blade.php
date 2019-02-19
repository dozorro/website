@if($post)
<div class="{{ !empty($is_small) ? 'c-n__item' : 'c-blog__news-item' }}">
    <div class="sb-new-card">
        <a href="{{ route('page.blog.post', ['slug' => $post->slug]) }}" class="sb-new-card__img">
			<div class="sb-new-card__img-bg" style="background-image: url('{{ $post->photo() }}');"></div>
		</a>
        <div class="sb-new-card__content-wrap">
            <div class="sb-new-card__row">
                <a href="{{ route('page.blog.by_author', ['slug' => $post->author->slug]) }}" class="sb-new-card__author">{{ $post->getAuthor()->full_name }}</a>
                <ul class="sb-new-card__tags-list">
                    @foreach($post->getTags() as $tag)
                        <li><a href="{{ route('page.blog.by_tag', ['slug' => $tag->slug ]) }}">{{ $tag->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="sb-new-card__row">
                <h2><a href="{{ route('page.blog.post', ['slug' => $post->slug]) }}">{{ $post->title }}</a></h2>
                <h3 class="@if(mb_strlen($post->short_description) > 200) {{'blog-maxheight'}} @endif">{!! strip_tags($post->short_description) !!}</h3>
                @if(mb_strlen($post->short_description) > 200)
                    <div class="link-more js-more">
                        <span class="show_more">{{ t('tender.show_all_text')}}</span>
                        <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                    </div>
                @endif
            </div>
            <div class="sb-new-card__row">
                <div class="sb-new-card__date">@datetime($post->created_at)</div>
                {{--<a href="#" class="sb-new-card__comments">0</a>--}}
            </div>
        </div>
    </div>
</div>
@endif