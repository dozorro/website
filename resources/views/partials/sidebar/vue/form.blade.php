@if(!empty($user->ngo))
    <div class="tender-header__descriptions tender-header-block">
        <div class="tender-header__wrap tender-header__descr">
            <a :href="'https://dozorro.org/tender/'+tender.tenderID" target="_blank" class="tender-header__descr-title risks-title risks-title-toggled" style="margin: 0 auto;text-align:center">{{ t('indicators.open_tender') }}</a>
        </div>
    </div>
@endif