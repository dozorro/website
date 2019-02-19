<div class="inline-layout col-2">
    <div class="block_about_company_go__item">
        <h4>{{ t('ngo.header.sum_under_control') }}</h4>
        <h3 style="font-size: 24px;">{{ $ngo->additional->sum_under_control }} {{ t('ngo.header.sum_currency') }}</h3>
    </div>
    <div class="block_about_company_go__item">
        <h4>{{ t('ngo.header.total_under_control') }}</h4>
        <div class="inline-layout">
            <h3 style="font-size: 24px;">{{ $ngo->additional->total_under_control }} {{ t('ngo.header.total_tenders') }}</h3>
            <div class="pie_diagram">
                <div class="pie" data-second="{{ $ngo->additional->total_above }}" data-first="{{ $ngo->additional->total_below }}"></div>
                <div class="info_pie">
                    <div>
                        <div class="item bg_grey">{{ t('ngo.header.total_below') }} {{ $ngo->additional->total_below }}</div>
                        <div class="item bg_pink">{{ t('ngo.header.total_above') }} {{ $ngo->additional->total_above }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="block_about_company_go__item">
        <h4>{{ t('ngo.header.f201_in_regions') }}</h4>
        <p>{{ $ngo->additional->f201_count }}</p>
    </div>
    <div class="block_about_company_go__item">
        <h4>{{ t('ngo.header.f202_f203_counts') }}</h4>
        <p>{{ $ngo->additional->f202_count }} {{ t('ngo.header.f202_counts') }} / {{ $ngo->additional->f203_count }} {{ t('ngo.header.f203_counts') }}</p>
    </div>
    <div class="block_about_company_go__item">
        <h4>{{ t('ngo.header.total_abuse') }}</h4>
        <p>{{ $ngo->additional->total_abuse }}</p>
    </div>
    <div class="block_about_company_go__item">
        <h4>{{ t('ngo.header.activities') }}</h4>
        <p>{{ $ngo->additional->month_total }} {{ t('ngo.header.tender_month') }}</p>
    </div>
</div>

<div class="ngo_progress">
    <div class="progress_info inline-layout">
        <div class="item bg_red">{{ t('ngo.header.#defeat') }}</div>
        <div class="item bg_green">{{ t('ngo.header.#success') }}</div>
        <div class="item bg_grey">{{ t('ngo.header.#cancel') }}</div>
    </div>
    <div class="progress">
        <div class="progress-bar bg_red" role="progressbar" style="width: {{ $ngo->additional->f204_defeat_percent }}%" aria-valuenow="{{ $ngo->additional->f204_defeat_percent }}" aria-valuemin="0" aria-valuemax="100">
            <div class="info"><div>{{ $ngo->additional->f204_defeat }} {{ t('ngo.header.defeat_count') }}</div></div>
        </div>
        <div class="progress-bar bg_green" role="progressbar" style="width: {{ $ngo->additional->f204_succes_percent }}%" aria-valuenow="{{ $ngo->additional->f204_succes_percent }}" aria-valuemin="0" aria-valuemax="100">
            <div class="info"><div>{{ $ngo->additional->f204_succes }} {{ t('ngo.header.success_count') }}</div></div>
        </div>
        <div class="progress-bar bg_grey" role="progressbar" style="width: {{ $ngo->additional->f204_cancel_percent }}%" aria-valuenow="{{ $ngo->additional->f204_cancel_percent }}" aria-valuemin="0" aria-valuemax="100">
            <div class="info"><div>{{ $ngo->additional->f204_cancel }} {{ t('ngo.header.cancel_count') }}</div></div>
        </div>
    </div>
</div>