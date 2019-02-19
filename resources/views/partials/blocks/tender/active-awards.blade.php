@if(!empty($item->__active_award))
    <div class="margin-bottom-more">

            <div class="block_title">
                <h3>{{t('tender.active_awards_title')}}</h3>
            </div>
            @if (!empty($item->__active_award->__date))
                <p class="table-date">
                    {{t('tender.active_awards_date')}}: {{$item->__active_award->__date}}
                </p>
            @endif
            <div class="overflow-table">
                <table class="{{$item->__features_price<1?' long':' contract'}}">
                    <thead>
                        <tr>
                            <th>{{t('tender.active_awards_participant')}}</th>
                            <th>{{t('tender.active_awards_proposition')}}</th>
                            <th>{{t('tender.active_awards_documents')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td>
                            @include('partials/blocks/tender/active-award-supplier')
                            #{{$item->__active_award->suppliers[0]->identifier->id}}
                        </td>
                            <td>
                            {{str_replace('.00', '', number_format($item->__active_award->value->amount, 2, '.', ' '))}}
                            {{$item->__active_award->value->currency}}{{$item->__active_award->value->valueAddedTaxIncluded?t('tender.vat'):''}}
                        </td>
                        <td>
                            @if(!empty($item->__active_award->documents))
                                <a href="" class="document-link2" data-id="{{$item->__active_award->id}}">{{t('tender.bids_documents')}}</a>
                            @else
                                {{t('tender.no_documents')}}
                            @endif
                        </td>
                    </tbody>
                </table>
            </div>

        
        @if(!empty($item->__active_award->documents))
            <div class="overlay overlay-documents">
                <div class="overlay-close overlay-close-layout"></div>
                <div class="overlay-box">
                    <div class="tender--offers documents" data-id="{{$item->__active_award->id}}">
                        <h4 class="overlay-title">
                            {{t('tender.bids_documents')}}
                        </h4>
                        @foreach($item->__active_award->documents as $document)
                            <div class="document-info">
                                <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                            </div>
                        @endforeach
                    </div>
                    <div class="overlay-close"><i class="sprite-close-grey"></i></div>
                </div>
            </div>
        @endif
    </div>
@endif