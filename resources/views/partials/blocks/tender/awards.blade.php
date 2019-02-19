@if (!empty($item->awards) && $item->procurementMethod=='open')
    <div class="margin-bottom-more" id="block_awards{{ $lot_id  ? '_lot' : '' }}">

            <div class="block_title">
                <h3>{{ t('tender.protocol_disclosure') }}</h3>
            </div>

            <div class="overflow-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{t('tender.awards_participant')}}</th>
                            <th>{{t('tender.awards_result')}}</th>
                            <th>{{t('tender.awards_proposition')}}</th>
                            <th>{{t('tender.awards_published')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->awards as $award)
                            <tr>
                                <td>
                                    @if(!empty($award->suppliers[0]->identifier->legalName))
                                        {{$award->suppliers[0]->identifier->legalName}}<br>
                                    @elseif(!empty($award->suppliers[0]->name))
                                        {{$award->suppliers[0]->name}}<br>
                                    @endif
                                    #{{$award->suppliers[0]->identifier->id}}
                                    @if(!empty($profileAccess) || !empty($user->is_profile_links))
                                        <br><a class="profile-role2" href="{{ route('page.profile_by_id', ['scheme'=>$award->suppliers[0]->identifier->scheme.'-'.$award->suppliers[0]->identifier->id,'tpl'=>$profileRole2TplId,'role'=>'role2']) }}">{{ t('dozorro_profile') }}</a>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($award->documents))
                                        <a href="" class="document-link2" data-id="{{$award->id}}-award">
                                    @endif
                                    @if($award->status=='unsuccessful')
                                        {{t('tender.big_status_unsuccessful')}}
                                    @elseif($award->status=='active')
                                        {{t('tender.big_status_active')}}
                                    @elseif($award->status=='pending')
                                        {{t('tender.big_status_pending')}}
                                    @elseif($award->status=='cancelled')
                                        {{t('tender.big_status_cancelled')}}
                                    @else
                                        {{$award->status}}
                                    @endif
                                    @if(!empty($award->documents))
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    {{str_replace('.00', '', number_format($award->value->amount, 2, '.', ' '))}}
                                    {{$award->value->currency}}{{$award->value->valueAddedTaxIncluded?t('tender.vat'):''}}
                                </td>
                                <td>
                                    {{date('d.m.Y H:i', strtotime($award->date))}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="overlay overlay-documents">
                <div class="overlay-close overlay-close-layout"></div>
                <div class="overlay-box">
                    @foreach($item->awards as $award)
                        @if (!empty($award->documents))
                            <div class="tender--offers documents" data-id="{{$award->id}}-award">
                            <h4 class="overlay-title">{{t('tender.bids_documents')}}</h4>
                                @foreach($award->documents as $document)
                                    <div class="document-info">
                                        <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                        <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    <div class="overlay-close"><i class="sprite-close-grey"></i></div>
                </div>
            </div>                            

    </div>
@endif