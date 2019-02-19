<div class="documents-tabs" id="block_docs{{ $lot_id  ? '_lot' : '' }}">
    <div class="margin-bottom margin-bottom-more">
        <div class="block_title">
            <h3>{{t('tender.tender_documentation')}}</h3>
        </div>
        @if (!empty($item->__tender_documents))
            @if(!empty($item->__tender_documents_stroked))
                <div class="bs-example bs-example-tabs lots-tabs wide-table" data-js="lot_tabs" data-tab-class="tab-document-content{{!empty($lot_id)?'-lot-'.$lot_id:''}}">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach([t('tender.last_edited'), t('tender.history_change')] as $k=>$group)
                            <li role="presentation" class="{{$k==0?'active':''}}">
                                <a href="" role="tab" data-toggle="tab" aria-expanded="{{$k==0?'true':'false'}}">{{$group}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
    
                <div class="tab-content tab-document-content{{!empty($lot_id)?'-lot-'.$lot_id:''}} active">
            @endif
    
            <div class="list_doc">

                    @foreach ($item->__tender_documents as $k=>$document)
                        @if(empty($document->stroked))
                            <div class="item inline-layout">
                                <div class="date">{{!empty($document->dateModified) ? date('d.m.Y H:i', strtotime($document->dateModified)) : t('tender.no_date')}}</div>
                                <div class="name_doc"><a href="{{$document->url}}" target="_blank" class="word-break">{{$document->title=='sign.p7s'?t('tender.digital_signature'):$document->title}}</a></div>
                            </div>
                        @endif
                    @endforeach

            </div>
    
            @if(!empty($item->__tender_documents_stroked))
                </div>
                <div class="tab-content tab-document-content{{!empty($lot_id)?'-lot-'.$lot_id:''}}">
                    <div class="list_doc">
                        @foreach ($item->__tender_documents as $k=>$document)
                            @if(empty($document->stroked))
                                <div class="item inline-layout">
                                    <div class="date">{{!empty($document->dateModified) ? date('d.m.Y H:i', strtotime($document->dateModified)) : t('tender.no_date')}}</div>
                                    <div class="name_doc">
                                        <a href="{{$document->url}}" target="_blank" class="word-break{{!empty($document->stroked) ? ' stroked': ''}}">{{$document->title=='sign.p7s'?t('tender.digital_signature'):$document->title}}</a>
                                    </div>
                                    <div class="list_doc">

                                        @foreach ($item->__tender_documents as $c=>$d)
                                            @if($d->id==$document->id && !empty($d->stroked))
                                                <div class="item inline-layout">
                                                    <div class="date">
                                                        {{!empty($d->dateModified) ? date('d.m.Y H:i', strtotime($d->dateModified)) : t('tender.no_date')}}
                                                    </div>
                                                    <div class="name_doc">
                                                        <a href="{{$d->url}}" target="_blank" class="word-break stroked">{{$d->title=='sign.p7s'?t('tender.digital_signature'):$d->title}}</a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="tender--customer padding-td">{{t('tender.no_documents')}}</div>
        @endif
    </div>
</div>