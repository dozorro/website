@if (!empty($item->__qualifications))
    <div class="margin-bottom-more">

            <div class="block_title">
                <h3>Протокол розгляду</h3>
            </div>
            <div class="overflow-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{t('tender.bids_participant')}}</th>
                            <th>{{t('tender.documents')}}</th>
                            <th>{{t('tender.decision')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->__qualifications as $qualification)
                            <tr>
                                <td>{{$qualification->__name}}</td>
                                <td>
                                    @if(!empty($qualification->__bid_documents_public) || !empty($qualification->__bid_documents_confident))
                                        <a href="" class="document-link" data-id="{{$qualification->id}}-bid">{{t('tender.bids_documents')}}</a>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        @if($item->status=='cancelled')
                                            @if($qualification->eligible && $qualification->qualified)
                                                {{t('tender.admitted_auction')}}
                                            @else
                                                {{t('tender.rejected')}}
                                            @endif
                                        @else
                                            {{t('tender.qualification_status.'.$qualification->status)}}
                                        @endif
                                    </div>
                                    @if(!empty($qualification->documents))
                                        <a href="" class="document-link2" data-id="{{$qualification->id}}-status">{{t('tender.bids_documents')}}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="overlay overlay-documents">
                <div class="overlay-close overlay-close-layout"></div>
                <div class="overlay-box">
                    @foreach($item->__qualifications as $qualification)
                        @if(!empty($qualification->documents))
                            <div class="tender--offers documents" data-id="{{$qualification->id}}-status">
                                @if(!empty($qualification->documents))
                                    <h4 class="overlay-title">
                                        {{t('tender.documents')}}
                                    </h4>
                                    @foreach($qualification->documents as $document)
                                        <div class="document-info">
                                            <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                            <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                        <div class="tender--offers documents" data-id="{{$qualification->id}}-bid">
                            @if(!empty($qualification->__bid_documents_public))
                                <h4 class="overlay-title">
                                    {{t('tender.public_documents')}}
                                </h4>
                                @foreach($qualification->__bid_documents_public as $document)
                                    <div class="document-info">
                                        <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                        <a href="{{$document->url}}" target="_blank" class="document-name">{{$document->title}}</a>
                                    </div>
                                @endforeach
                            @endif
                            @if(!empty($qualification->__bid_documents_confident))
                                <h4 class="overlay-title">
                                    {{t('tender.confidential_documents')}}
                                </h4>
                                @foreach($qualification->__bid_documents_confident as $document)
                                    <div class="document-info">
                                        <div class="document-date">{{date('d.m.Y H:i', strtotime($document->datePublished))}}</div>
                                        <div>{{$document->title}}</div>
                                        <p style="font-size:80%;margin-top:10px;margin-bottom:4px;color:#AAA">{{t('tender.justification_privacy')}}</p>
                                        @if(!empty($document->confidentialityRationale))
                                            <p style="font-size:80%;">{{$document->confidentialityRationale}}</p>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                    <div class="overlay-close"><i class="sprite-close-grey"></i></div>
                </div>
            </div>

    </div>
@endif