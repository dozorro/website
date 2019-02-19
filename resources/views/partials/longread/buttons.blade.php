<div class="block_info_foto_home">

    <div class="container inline-layout">
        <div class="block_text">
            @if(!empty($data->block_title))
                <h2>{{ $data->block_title }}</h2>
            @endif
            @if(!empty($data->block_text))
                <div class="text_holder">{{ $data->block_text }}</div>
            @endif
            <form class="search-form" method="GET" action="/search">
                <div class="input-container">
                    <input type="text" name="tid" placeholder="{{ t('search_form_home.query') }}">
                    <input type="hidden" name="sort" value="dateModified">
                    <input type="hidden" name="order" value="desc">
                    <div class="input-info"><a href="/search/#filters">{{ t('search_form_home.filters') }}</a></div>
                </div>
                <button type="submit" class="submit-btn">{{ t('search_form_home.submit') }}</button>
                
            </form>
            {{-- <div class="button_holder">
                <a href="{{ route('search', ['search' => 'tender']) }}">{{t('longreads.tenders_search')}}</a>
                <a href="{{ route('page.tenders') }}">{{t('longreads.all_reviews')}}</a>
            </div>
            --}}
        </div>
        {{-- <div class="img-holder">
            <img src="/assets/images/img_home.png">
        </div>
        --}}
    </div>

</div>