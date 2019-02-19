@if(!empty($item->__documents))
    <div class="margin-bottom-more" id="block_contract{{ $lotID  ? '_lot' : '' }}">

            <div class="block_title">
                <h3 class="href-left">{{t('tender.contract_title')}}</h3>
                @if(!empty($item->__signed_contracts[0]->period->startDate))
                    <br>
                    {{t('tender.contract_period')}} {{ date('d.m.Y H:i', strtotime($item->__signed_contracts[0]->period->startDate)) . ' - ' . date('d.m.Y H:i', strtotime($item->__signed_contracts[0]->period->endDate)) }}
                @endif
                @if(!empty($item->__signed_contracts[0]->contractNumber))
                    <br>
                    {{t('tender.contract_number')}} {{ $item->__signed_contracts[0]->contractNumber }}
                @endif
            </div>
            @if ($item->__button_007)
                <a href="http://www.007.org.ua/search#{{('edrpou='.$item->__button_007->edrpou.'&date_from='.$item->__button_007->date_from.'&trans_filter={"partner":"'.$item->__button_007->partner.'","type":["outgoing"]}&find=true')}}" target="_blank">
                    {{t('tender.check_payment_for_payments_treasury')}}
                </a>
            @endif
            <div class="overflow-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{t('tender.contract')}}</th>
                            <th>{{t('tender.contract_status')}}</th>
                            <th>{{t('tender.contract_sum')}}</th>
                            <th>{{t('tender.contract_published')}}</th>
                            <th>{{t('tender.contract_signed')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->__documents as $document)
                            <tr>
                                <td><a href="{{$document->url}}" target="_blank">{{$document->title}}</a></td>
                                <td>
                                    @if(t('contract.'.$document->status))
                                        {{t('contract.'.$document->status)}}
                                    @else
                                        {{$document->status}}
                                    @endif
                                </td>
                                <td>
                                    @if($document->status=='active' && strpos($document->title, 'p7s')===false && !empty($item->__contracts_price))
                                        <div>{{ str_replace('.00', '', number_format($item->__contracts_price, 2, '.', ' '))}}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{date('d.m.Y H:i', strtotime($document->dateModified))}}</div>
                                </td>
                                <td>
                                    @if (!empty($document->dateSigned) && strpos($document->title, 'p7s')===false && !empty($item->__contracts_dateSigned))
                                        <div>{{date('d.m.Y', strtotime($item->__contracts_dateSigned))}}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

    </div>
@endif
