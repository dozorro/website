<div class="col-sm-4 margin-bottom ">
    <h3>{{t('tender.auction_info')}}</h3>
    <div class="gray-bg padding margin-bottom">
        <ul class="nav nav-list">
            @if (!empty($dataStatus[$item->status]))
                <li>
                    <strong>{{t('tender.status')}}:</strong><br>{{$dataStatus[$item->status]}}
                </li>
            @endif
            @if (!empty($item->value->amount))
                <li>
                    <strong>{{t('tender.budget')}}:</strong><br>{{number_format($item->value->amount, 0, '', ' ')}} {{$item->value->currency}}
                </li>
            @endif
            @if (!empty($item->minimalStep->amount))
                <li>
                    <strong>{{t('tender.min_step')}}:</strong><br>{{$item->minimalStep->amount}} {{$item->minimalStep->currency}}
                </li>
            @endif
            <li>
                <strong>{{t('tender.tender_id')}}:</strong><br>
                {{$item->tenderID}}
            </li>
        </ul>
        <div class="info-all-wr">
            <a href="" class="info-all">{{t('tender.more')}}</a>
        </div>
        <div class="overlay overlay-info-all">
            <div class="overlay-close overlay-close-layout"></div>
            <div class="overlay-box">
                <div class="tender--offers documents" data-id="info">
                    <h4 class="overlay-title">{{t('tender.auction_info')}}</h4>
                    <div class="document-info">
                        ID
                        <div class="document-date"><a href="https://public.api.openprocurement.org/api/0/tenders/{{$item->id}}" target="_blank">{{$item->id}}</a></div>
                    </div>
                    @if (!empty($item->__yaml_documents))
                        @foreach($item->__yaml_documents as $document)
                            <div class="document-info">
                                {{t('tender.auction_journal')}}
                                <div class="document-date"><a href="{{$document->url}}" target="_blank">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</a></div>
                            </div>                                                    
                        @endforeach
                    @endif
                    {{--
                    @if(Session('api')=='http://ocds-test.aws3.tk/search')
                        <div class="document-info">
                            JSON
                            <div class="document-date"><a href="http://ocds-test.aws3.tk/search?tid={{$item->tenderID}}" target="_blank">ocds-test.aws3.tk</a></div>
                        </div>
                        <div class="document-info">
                            ips.vdz.ua
                            <div class="document-date"><a href="https://ips.vdz.ua/ua/purchase_details.htm?id={{$item->id}}" target="_blank">ips.vdz.ua</a></div>
                        </div>
                    @endif
                    --}}
                </div>
                <div class="overlay-close"><i class="sprite-close-grey"></i></div>
            </div>
        </div>
    </div>
</div>