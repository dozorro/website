@if (!empty($item->items) && !$item->__isMultiLot)
    <div class="margin-bottom">
        <div{{empty($item->features) ? 'class="border-bottom"':''}}>
            <h3>{{t('tender.positions')}}</h3>

            @foreach($item->items as $one)
                <div class="row margin-bottom">
                    <div class="col-md-4 col-md-push-8">
                        <div class="padding margin-bottom">
                            {{!empty($one->quantity)?$one->quantity.t('tender.q'):''}}
                        </div>
                    </div>
                    <div class="col-md-8 col-md-pull-4 description-wr{{!empty($one->description) && mb_strlen($one->description)>350?' croped':' open'}}">
                        @if (!empty($one->description))
                            <div class="tender--description--text description">
                                {!!nl2br($one->description)!!}
                            </div>
                            @if (mb_strlen($one->description)>350)
                                <a class="search-form--open"><i class="sprite-arrow-down"></i>
                                    <span>{{t('interface.expand')}}</span>
                                    <span>{{t('interface.collapse')}}</span>
                                </a>
                            @endif
                        @endif
                        @if (!empty($one->classification))
                            <div class="tender-date">{{t('tender.cpv')}}: {{$one->classification->id}} — {{$one->classification->description}}</div>
                        @else
                            <div class="tender-date">{{t('tender.no_cpv')}}</div>
                        @endif
                        @if(!empty($one->additionalClassifications[0]))
                            <div class="tender-date">{{t('tender.dkpp')}}: {{$one->additionalClassifications[0]->id}} — {{$one->additionalClassifications[0]->description}}</div>
                        @else
                            <div class="tender-date">{{t('tender.no_dkpp')}}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif