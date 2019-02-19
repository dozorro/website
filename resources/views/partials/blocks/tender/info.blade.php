<div class="margin-bottom margin-bottom-more" id="block_info">

            <div class="block_title">
                <h3>{{t('tender.purchase_information')}}</h3>
                <div class="label_title">{{t('tender.procurement_title')}}</div>
            </div>
            <div class="">

                @if (!empty($item->description))
                    <div class="row">
                        <div class="col-md-12 description-wr margin-bottom">
                            <div class="tender--description--text description{{mb_strlen($item->description)>350?' croped':' open'}}">
                                {!!nl2br($item->description)!!}
                            </div>
                            @if (mb_strlen($item->description)>350)
                                <a class="search-form--open" href="">

                                    <span>{{t('interface.expand')}}</span>
                                    <span>{{t('interface.collapse')}}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif


                @if(!empty($item->items) && !$item->__isMultiLot)
                    <h3>{{t('tender.description_individual_part_or_parts_of_procurement_subject')}}</h3>


                    @foreach($item->items as $one)
                        <div class="row margin-bottom">
                            <div class="col-md-4 col-md-push-8">
                                <div class="padding margin-bottom">
                                    {{!empty($one->quantity)?$one->quantity:''}} @if(!empty($one->unit->code)){{t('measures.'.$one->unit->code.'.symbol')}}@endif
                                </div>
                            </div>
                            <div class="col-md-8 col-md-pull-4 description-wr{{!empty($one->description) && mb_strlen($one->description)>350?' croped':' open'}}">
                                @if (!empty($one->description))
                                    <div class="tender--description--text description ">
                                        {!!nl2br($one->description)!!}
                                    </div>
                                    @if (mb_strlen($one->description)>350)
                                        <a class="search-form--open">
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
                                @if(!empty($one->additionalClassifications))
                                    @foreach($one->additionalClassifications as $classification)
                                        <div class="tender-date">{{t('scheme.'.$classification->scheme)}}: {{$classification->id}} — {{$classification->description}}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

</div>