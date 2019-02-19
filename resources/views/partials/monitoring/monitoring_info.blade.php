<div class="block_info_go ">
    <div class="block_title_img mb20 inline-layout">
        <div class="block_title_go inline-layout">
            <h1>
                @if($profile_link)
                    <a href="{{ route('page.monitoring', ['slug' => $monitor->slug]) }}">{{ $monitor->title }}</a>
                @else
                    {{ $monitor->title }}
                @endif
            </h1>
            @if($monitor->description)
                <div class="info">
                    <span class="info_icon"></span>
                    <div class="info_text">
                        <div>
                            {{ $monitor->description }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($monitor->logo)
            <div class="img-holder block_img_go">
                <img src="{{ $monitor->logo }}" alt="{{ $monitor->title }}">
            </div>
        @endif
    </div>

    <div class="block_about_company_go row">
        <div class="col-md-6">
            @if($monitor->users)
                <div class="block_about_company_go__item">
                    <h4>{{ t('page.monitor.users') }}</h4>
                    <p>
                        @foreach($monitor->getUsers() AS $m_user)
                            <span>{{ $m_user->full_name }}
                                @if($m_user->fb)
                                    <a href="{{ $m_user->fb }}" target="_blank" class="facebook_icon"></a>
                                @endif
                            </span>
                        @endforeach
                    </p>
                </div>
            @endif
            <div class="inline-layout col-3">
                @if($monitor->phone)
                    <div class="block_about_company_go__item">
                        <h4>{{ t('page.monitor.phone') }}</h4>
                        <p><a href="tel:{{ $monitor->phone }}">{{ $monitor->phone }}</a></p>
                    </div>
                @endif
                @if($monitor->site)
                    <div class="block_about_company_go__item">
                        <h4>{{ t('page.monitor.site_link') }}</h4>
                        <p><a href="{{ $monitor->site }}" target="_blank">{{ $monitor->site }}</a></p>
                    </div>
                @endif
                @if($monitor->fb)
                    <div class="block_about_company_go__item">
                        <h4>{{ t('page.monitor.fb_link') }}</h4>
                        <p><a href="{{ $monitor->fb }}" target="_blank">{{ $monitor->fb }}</a></p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            @if($monitor->region)
                <div class="block_about_company_go__item">
                    <h4>{{ t('page.monitor.regions') }}</h4>
                    <p>{{ $monitor->showRegions() }}</p>
                </div>
            @endif
            <div class="inline-layout col-2">
                @if($monitor->activity)
                    <div class="block_about_company_go__item">
                        <h4>{{ t('page.monitor.activity') }}</h4>
                        <p class="@if(mb_strlen($monitor->activity) > 10) {{'maxheight'}} @endif">{{ $monitor->activity }}</p>
                        @if(mb_strlen($monitor->activity) > 10)
                            <div class="link-more js-more">
                                <span class="show_more">{{ t('tender.show_all_text')}}</span>
                                <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>