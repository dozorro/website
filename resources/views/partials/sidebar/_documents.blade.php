@if(!empty($item->__tender_documents))
<div class="tender-header__purchase-subjects tender-header-block">
    <div class="tender-header__wrap tender-header__descr tender-header__purchase-subject {{ @$class }} toggled">
        <button class="tender-header__descr-toggle"></button>
        <div class="block-title">{{ t('indicators.docs_block') }}</div>
        @if(!$hide)
        <div class="tender-header_info__item toggled">
            <div class="tender-header__descr-item">
                <div class="detail-value">{{ count(@$item->__tender_documents) . ' '. t('indicators.docs_count') }}</div>
            </div>
        </div>
        @endif

        @if(!empty($item->__tender_documents))
        <div class="tender-header_info__item">
            <div class="tender-header__descr-item">
                <div class="detail-value">{{ count(@$item->__tender_documents) . ' '. t('indicators.docs_count') }}</div>
                <div class="kick-item">
                    <div class="kick-item-info-group">
                            <div class="sub-group">
                                <?php $i = 0; ?>
                                @foreach($item->__tender_documents as $doc)
                                    @if($i < 2 && !strpos($doc->title, '.p7s'))
                                    <?php $i++; ?>
                                    <div class="sub-group-value-item">
                                        <div class="sub-group-value"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                    </div>
                </div>
                <button class="tender-header__descr-title risks-title show-docs">{{ t('indicators.show_docs') }}</button>
                <div class="kick-item hidden">
                    <div class="kick-item-info-group">
                        <div class="sub-group">
                            @foreach($item->__tender_documents as $doc)
                                @if(!strpos($doc->title, '.p7s'))
                                    <div class="sub-group-value-item">
                                        <div class="sub-group-value"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <button class="tender-header__descr-title risks-title show-docs hidden">{{ t('indicators.show_detail_docs') }}</button>
                <div class="kick-item hidden">
                    <div class="kick-item-info-group">
                        <div class="sub-group">
                            @foreach($item->__tender_documents as $doc)
                                <div class="sub-group-value-item">
                                    <div class="sub-group-value"><a download href="{{ $doc->url }}">{{ $doc->title }}</a></div>
                                    <div class="sub-group-value-descr">{{ Carbon\Carbon::createFromTimestamp(strtotime($doc->dateModified))->format('d.m.Y H:i') }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(!empty($item->__tender_documents_stroked))
            <div class="tender-header__descr-item tender-header__descr-risks docs-history-block hidden">
                <button class="tender-header__descr-title risks-title docs-history">{{ t('tender.history_change') }}</button>
                <div class="risks-items hidden">
                    <div class="risk-values">
                        @foreach ($item->__tender_documents as $k=>$document)
                            @if(empty($document->stroked))
                                @foreach ($item->__tender_documents as $c=>$d)
                                    @if($d->id==$document->id && !empty($d->stroked))
                                        <div class="item inline-layout">
                                            <div class="date">{{!empty($document->dateModified) ? date('d.m.Y H:i', strtotime($document->dateModified)) : t('tender.no_date')}}</div>
                                            <div class="name_doc">
                                                <a href="{{$document->url}}" target="_blank" class="word-break{{!empty($document->stroked) ? ' stroked': ''}}">{{$document->title=='sign.p7s'?t('tender.digital_signature'):$document->title}}</a>
                                            </div>
                                            <div class="list_doc">
                                                <div class="item inline-layout">
                                                    <div class="date">
                                                        {{!empty($d->dateModified) ? date('d.m.Y H:i', strtotime($d->dateModified)) : t('tender.no_date')}}
                                                    </div>
                                                    <div class="name_doc">
                                                        <a href="{{$d->url}}" target="_blank" class="word-break stroked">{{$d->title=='sign.p7s'?t('tender.digital_signature'):$d->title}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endif