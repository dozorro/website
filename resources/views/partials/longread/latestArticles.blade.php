@if(!$block->data['articles']->isEmpty() || $block->data['main_article'])
    <div class="c-b">
        <div class="container">
            <div class="row c-b__relative-row block_last_news">
                @if(!$block->data['articles']->isEmpty())
                <div class="col-md-4 c-b__left item">
                    <div class="c-list-card">
                        <div class="c-list-card__inner">
                            <h3 class="c-list-card__header">{{t('blog.latest_news')}}</h3>
                            <div class="c-list-card__cards">
                                @foreach($block->data['articles'] as $item)
                                    @if($item)
                                        <div class="sb-list-item">
                                            <div class="sb-list-item__row">
                                                <span><a class="{{ $item->is_bold ? 'is_bold' : 'is_normal' }} {{ $item->is_red ? 'is_red' : 'is_black' }}" href="{{ route('page.news.post', ['slug' => $item->slug]) }}">{{ $item->title }}</a></span>
                                            </div>
                                            <div class="sb-list-item__row">
                                                <div class="sb-list-item__date">@datetime($item->created_at)</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="c-list-card__link-wrap">
                                <a href="{{ route('page.news') }}">{{t('blog.all_news')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
    
                <?php $main_article = $block->data['main_article']; ?>
    
                @if($main_article)
                    <div class="col-md-8 c-height c-b__sb-b-wrap item">
                        <div class="sb-b">
                            <div class="sb-b__bgimg" style="background-image: url('{{ $main_article->photo() }}');"></div>
                            <a href="{{ route('page.blog.post', ['slug' => $main_article->slug]) }}" class="sb-b__link"></a>
                            @if($main_article->group)<a href="javascript:;" class="sb-b__tag">{{ $main_article->group }}</a>@endif
                            <div class="sb-b__title-wrap">
                                <div class="sb-b__title-row">
                                    <h2>{{ $main_article->title }}</h2>
                                </div>
                                <div class="sb-b__title-row">
                                    @if($main_article->budget)<div class="sb-b__info">{{t('blog.budget')}} {{$main_article->budget}}</div>@endif
                                    @if($main_article->sum)<div class="sb-b__info">{{t('blog.sum')}} {{$main_article->sum}}</div>@endif
                                    @if($main_article->saving)<div class="sb-b__info">{{t('blog.saving')}} {{$main_article->saving}}</div>@endif
                                </div>
                            </div>
                            <div class="sb-b__bottom-row">
                                <!--<div class="sb-b__date">@datetime($main_article->published_at)</div><span class="sb-b__commtents">0</span>-->
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif