@if($article)
    <div class="c-n__item">
        <div class="sb-new-card">
            <div class="sb-new-card__top-img">
                <a href="{{ route('page.blog.post', ['slug' => $article->slug]) }}" class="sb-new-card__img">
					<div class="sb-new-card__img-bg" style="background-image: url('{{ $article->photo() }}');"></div>
				</a>
                @if($article->group)<a href="javascript:;" class="sb-new-card__color-tag">{{ $article->group }}</a>@endif
                <div class="sb-new-card__info-wrap">
                    @if($article->budget)<div>{{t('blog.budget')}} {{$article->budget}}</div>@endif
                    @if($article->sum)<div>{{t('blog.sum')}} {{$article->sum}}</div>@endif
                    @if($article->saving)<div>{{t('blog.saving')}} {{$article->saving}}</div>@endif
                </div>
            </div>
            <div class="sb-new-card__content-wrap">
                <div class="sb-new-card__row">
                    <h2><a href="{{ route('page.blog.post', ['slug' => $article->slug]) }}">{{ $article->title }}</a></h2>
                </div>
                <div class="sb-new-card__row">
                    <div class="sb-new-card__date">@datetime($article->created_at)</div>
                    {{--<span class="sb-new-card__comments">5</span>--}}
                    {{--<span class="sb-new-card__like">+821</span>--}}
                    {{--<span class="sb-new-card__dislike">-30</span>--}}
                </div>
            </div>
        </div>
    </div>
@endif