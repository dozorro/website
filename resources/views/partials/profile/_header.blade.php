<div class="block_profile1 bg_grey">
    <div class="container">
        <div class="bg_white">
            <div class="row sligle-profile-head-title">
                <div class="col-md-12">
                    <h1>{{ $profile->name }}</h1>
                </div>
            </div>
            <div class="row single-profile-head-info">
                <div class="col-lg-8 col-md-7">

                    <div class="list_info_profile inline-layout">
                        <div class="item user-info">
                            <div class="title">
                                @if($profile->partyRole != 'tenderer')
                                    {{ t('profile.header.label-1.1') }}
                                @else
                                    {{ t('profile.header.label-1.2') }}
                                @endif
                            </div>
                            <div class="value">
                                @if($profile->partyRole != 'tenderer')
                                    {{ $profile->showType() }}
                                @else
                                    {{ $profile->director }}
                                @endif
                            </div>
                        </div>
                        <div class="item tel-info" >
                            <div class="title">
                                {{ t('profile.phone') }}
                            </div>
                            <div class="value"><a href="tel:{{ $profile->phone }}">{{ $profile->phone }}</a></div>
                        </div>
                        <div class="item edrpou-info" >
                            <div class="title">
                                {{ t('profile.edrpou') }}
                            </div>
                            <div class="value">
                                <span>{{ $edrpou }}</span>
                            </div>
                        </div>
                        <div class="item address-info">
                            <div class="title">
                                {{ t('profile.address') }}
                            </div>
                            <div class="value"><address>
                                    {{ $profile->address }}
                                </address></div>
                        </div>
                        <div class="item email-info" >
                            <div class="title">
                                {{ t('profile.email') }}
                            </div>
                            <div class="value"><a href="mailto:{{ $profile->email }}">{{ $profile->email }}</a></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5">
                    @if(!empty($riskAccess))
                    <div class="block_rating_profile inline-layout" >
                        <div class="rating_info">
                            <h4>
                                <span>{{ t('profile.index') }}</span>
                                <span class="info">
                                    <span class="info_icon2">?</span>
                                    <div class="info_text">
                                        <div>
                                            <p>
                                                {{ t('profile.index_text') }}
                                            </p>
                                            <p>
                                                <a href="#">{{ t('profile.index_detail') }}</a>
                                            </p>
                                        </div>
                                    </div>
                                </span>
                            </h4>
                            <div class="rating">
                                {{ $profileRiskTotal }}
                                {{ t('profile.index_percent') }}
                            </div>                            
                        </div>

                        @if($profileRiskTotalDec)
                            <div class="rating_stars @if(empty($profileRiskTotal)){{'disabled'}}@endif ">
                                @foreach(range(1, 10) as $index)
                                    <div class="star @if($profileRiskTotalDec >= $index){{'active'}}@endif @if($profileRiskTotalDec >= $index && $profileRiskTotalDec < $index+1 && $profileRiskTotalHalf <= 0.5){{'half'}}@endif"></div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>