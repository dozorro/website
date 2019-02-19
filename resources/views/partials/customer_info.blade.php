<div class="tender_customer">
    <div class="inline-layout">
        <div class="img-holder mobile">
            @if($image = $customer->image())
                <img src="{{ $image }}" alt="{{ $customer->name }}">
            @endif
        </div>
        <div class="info_tender_customer">
            <h3>
                <a href="{{ route('page.customers', ['slug' => $customer->main_edrpou]) }}">{{ $customer->title }}</a>
            </h3>
            <div class="info_customer">
                <div class="item inline-layout">
                    <div class="name">{{ t('tenders.tenders_count') }}</div>
                    <div class="value">
                        {{ $customer->tenders_count() }}
                    </div>
                </div>
                <div class="item inline-layout">
                    <div class="name">{{ t('tenders.tenders_sum') }}</div>
                    <div class="value">
                        {{ number_format($customer->tenders_sum(), 0, '', ' ') . ' ' . t('tenders.currency')}}
                    </div>
                </div>
                <div class="item inline-layout">
                    <div class="name">{{ t('tenders.tenders_reviews') }}</div>
                    <div class="value">
                        {{ count($customer->all_forms()) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="img-holder">
            @if($image = $customer->image())
                <img src="{{ $image }}" alt="{{ $customer->name }}">
            @endif
        </div>
    </div>
</div>