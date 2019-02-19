@if($post)
<div class="list_news_feed">
    <div class="item_news_feed">
        <div class="time">{{ $post->created_at->format('H:i') }}</div>
        <div class="item-text">
            <a class="{{ $post->is_bold ? 'is_bold' : 'is_normal' }} {{ $post->is_red ? 'is_red' : 'is_black' }}" href="{{ route('page.news.post', ['slug' => $post->slug]) }}">{{ $post->title }}</a>
            {!! $post->short_description !!}
        </div>
    </div>
</div>
@endif