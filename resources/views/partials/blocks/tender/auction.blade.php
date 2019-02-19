@if (!empty($item->auctionPeriod->startDate) || !empty($item->auctionPeriod->endDate) || !empty($item->auctionUrl))
    <div class="margin-bottom-more" id="block_auction{{ $lot_id  ? '_lot' : '' }}">
        <div class="block_title">
            <h3>{{ t('tender.auction') }}</h3>
        </div>
        <div class="row" >
            <div class="col-md-6">
                @if (!empty($item->auctionPeriod->startDate))
                    <div class="tender-description__item">
                        <div class="tender-description__title">{{ t('tender.beginning') }}:</div>
                        <div class="tender-description__text">{{date('d.m.Y H:i', strtotime($item->auctionPeriod->startDate))}}</div>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                @if (!empty($item->auctionPeriod->endDate))
                    <div class="tender-description__item">
                        <div class="tender-description__title">{{ t('tender.end') }}:</div>
                        <div class="tender-description__text">{{date('d.m.Y H:i', strtotime($item->auctionPeriod->endDate))}}</div>
                    </div>
                @endif
            </div>

            @if(!empty($item->auctionUrl))
                <div class="col-md-12 margin-bottom"><a href="{{$item->auctionUrl}}" target="_blank">{{ t('tender.go_auction') }}</a></div>
            @endif
        </div>
    </div>

@endif