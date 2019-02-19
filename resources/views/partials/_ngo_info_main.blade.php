<div class="block_title_img mb20 inline-layout">
    <div class="block_title_go inline-layout">

        <h1>
            @if($profile_link)
                <a href="{{ route('page.ngo', ['slug' => $ngo->slug]) }}">{{ $ngo->title }}</a>
            @else
                {{ $ngo->title }}
            @endif
        </h1>
        @if($ngo->description)
            <div class="info">
                <span class="info_icon"></span>
                <div class="info_text">
                    <div>
                        {{ $ngo->description }}
                    </div>
                </div>
            </div>
        @endif

        <div style="margin-left: 25px;">
            <img src="{{ asset('assets/images/d-coin.png') }}" width="44" height="44" />
            <div style="display: inline-block;">
                <strong style="font-size: 24px;margin-left: 15px;">{{ (int)$ngo->coinsSum() }}</strong>
                        <span style="font-size: 18px;margin-left: 15px;display: table;color:silver;">{{ (int)$ngo->coinsTotalSum() }}
                            <div class="info" style="display: inline-block;">
                                <span class="info_icon"></span>
                                <div class="info_text" style="margin-left: -105px;width: 200px;">
                                    <div>
                                        {{ t('page.ngo.coins_desc') }}
                                    </div>
                                </div>
                            </div>
                        </span>
            </div>
        </div>
    </div>

    @if($ngo->logo)
        <div class="img-holder block_img_go">
            <img src="{{ $ngo->logo }}" alt="{{ $ngo->title }}">
        </div>
    @endif
</div>

<div class="block_about_company_go row">
    <div class="col-md-5">
        <div class="block_about_company_go__item">
            <h4>{{ t('page.ngo.certificate') }}</h4>
            @if($ngo->certificate)
                <p>
                    {{ t($ngo->certificateStatus) }}
                    <img style="width: 20px;margin-left: 20px;" src="{{ asset('assets/images/ngo/'.$ngo->certificate.'.svg') }}">
                </p>
            @endif
        </div>
        @if($ngo->users)
            <div class="block_about_company_go__item">
                <h4>{{ t('page.ngo.users') }}</h4>
                <p>
                    @foreach($ngo->getUsers() AS $ngo_user)
                        <span>{{ $ngo_user->full_name }}
                            @if($ngo_user->fb)
                                <a href="{{ $ngo_user->fb }}" target="_blank" class="facebook_icon"></a>
                            @endif
                            </span>
                    @endforeach
                </p>
            </div>
        @endif
        <div class="inline-layout col-3">
            @if($ngo->phone)
                <div class="block_about_company_go__item">
                    <h4>{{ t('page.ngo.phone') }}</h4>
                    <p><a href="tel:{{ $ngo->phone }}">{{ $ngo->phone }}</a></p>
                </div>
            @endif
            @if($ngo->site)
                <div class="block_about_company_go__item">
                    <h4>{{ t('page.ngo.site_link') }}</h4>
                    <p><a href="{{ $ngo->site }}" target="_blank">{{ $ngo->site }}</a></p>
                </div>
            @endif
            @if($ngo->fb)
                <div class="block_about_company_go__item">
                    <h4>{{ t('page.ngo.fb_link') }}</h4>
                    <p><a href="{{ $ngo->fb }}" target="_blank">{{ $ngo->fb }}</a></p>
                </div>
            @endif
        </div>
        @if($ngo->region)
            <div class="block_about_company_go__item">
                <h4>{{ t('page.ngo.regions') }}</h4>
                <p>{{ $ngo->showRegions() }}</p>
            </div>
        @endif
        <div class="inline-layout col-2">
            @if($ngo->activity)
                <div class="block_about_company_go__item">
                    <h4>{{ t('page.ngo.activity') }}</h4>
                    <p class="@if(mb_strlen($ngo->activity) > 10) {{'maxheight'}} @endif">{{ $ngo->activity }}</p>
                    @if(mb_strlen($ngo->activity) > 10)
                        <div class="link-more js-more">
                            <span class="show_more">{{ t('tender.show_all_text')}}</span>
                            <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                        </div>
                    @endif
                </div>
            @endif
            @if($ngo->count_authors_posts)
                <div class="block_about_company_go__item">
                    <h4>{{ t('page.ngo.news') }}</h4>
                    <p><a href="{{ route('page.blog.by_ngo', ['slug' => $ngo->slug]) }}">{{ $ngo->count_authors_posts }} {{ t('page.ngo.posts_count') }}</a></p>
                </div>
            @endif
            <div class="block_about_company_go__item">
                <h4>{{ t('page.ngo.reviews') }}</h4>
                <p>{{ $ngo->reviewsCount() }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-7 block_info_go_analytics_parent">

        <div class="block_info_go_analytics">
            @include('partials._ngo_analytics')
        </div>

        @if($showFilter)
            <div class="block_info_go_filter">

                    <div class="col-md-4" style="padding-left: 0px;">
                        <div class="input-group date">
                            <input placeholder="{{ t('blog.search.date_from') }}" name="date_from" type="text" class="form-control datepicker" value="{{ app('request')->input('date_from') }}">
                            <div class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group date">
                            <input placeholder="{{ t('blog.search.date_to') }}" name="date_to" type="text" class="form-control datepicker" value="{{ app('request')->input('date_to') }}">
                            <div class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="text-align: right;padding-right: 0px;">
                        <div class="input-group" style="display: inline-table;">
                            <button data-id="{{ $ngo->id }}" class="ngo-header-submit">{{ t('ngo.header.submit') }}</button>
                        </div>
                    </div>

                <div style="clear: both;"></div>

            </div>
        @endif

    </div>

</div>