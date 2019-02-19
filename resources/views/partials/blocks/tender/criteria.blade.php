@if($item->__icon!='pen' && $item->procurementMethod=='open')
    @if(empty($item->lots) || sizeof($item->lots)==1)

        <div class="criterii margin-bottom-more">
            <div class="block_title">
                <h3>{{t('tender.criteria_doc')}}</h3>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tender-description__item">
                        <div class="tender-description__title">
                            {{t('tender.price')}}:
                        </div>
                        <div class="tender-description__text">
                            {{$item->__features_price*100}}%
                        </div>

                    </div>
                    @if(!empty($item->__features))
                        @foreach($item->__features as $feature)
                            @if($feature->max>0)
                                <div class="tender-description__item">
                                    <div class="tender-description__title">{{!empty($feature->title) ? $feature->title : ''}}:</div>
                                    <div class="tender-description__text">{{$feature->max*100}}%</div>
                                </div>
                                @foreach($feature->enum as $enum)
                                    @if($enum->value>0)
                                        <div class="tender-description__item">
                                            <div class="tender-description__title">{{$enum->title}}:</div>
                                            <div class="tender-description__text">{{$enum->value*100}}%</div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    @endif
@endif