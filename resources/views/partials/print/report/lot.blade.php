<?php $start_n=$n; ?>

<br>
@foreach($lots as $k=>$lot)

    @if(!empty($lot->title) && sizeof($lots)>1)
        <center>
            <h3>ЛОТ {{$k+1}} — {{$lot->title}}</h3>
        </center>
        <?php $n=$start_n; ?>
    @endif
    
    @if(!empty($lot->__items) || !empty($item->items))
        <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
            <tr valign="top">
                <td>{{$n++}}. Конкретна назва предмета закупівлі</td>
                <td>{{$n++}}. Коди відповідних класифікаторів предмета закупівлі (за наявності)</td>
                <td>{{$n++}}. Кількість товарів або обсяг виконання робіт чи надання послуг</td>
                <td>{{$n++}}. Місце поставки товарів або місце виконання робіт чи надання послуг</td>
                <td>{{$n++}}. Строк поставки товарів, виконання робіт чи надання послуг</td>
            </tr>
            @foreach((!empty($lot->__items) ? $lot->__items : $item->items) as $one)
                <tr valign="top">
                    <td>
                        {{$one->description}}
                    </td>
                    <td>
                        @if (!empty($one->classification))
                            {{trans('tender.cpv')}}: {{$one->classification->id}} — {{$one->classification->description}}
                        @else
                            {{trans('tender.no_cpv')}}
                        @endif
                        <br>
                        @if(!empty($one->additionalClassifications[0]))
                            {{trans('tender.dkpp')}}: {{$one->additionalClassifications[0]->id}} — {{$one->additionalClassifications[0]->description}}
                        @else
                            <br>{{trans('tender.no_dkpp')}}
                        @endif                
                    </td>
                    <td>
                        {{!empty($one->quantity)?$one->quantity:''}} @if(!empty($one->unit->code)){{trans('measures.'.$one->unit->code.'.symbol')}}@endif
                    </td>
                    <td>
                        @if(!empty($one->deliveryAddress->streetAddress))
                            {{$one->deliveryAddress->postalCode}}, {{$one->deliveryAddress->region}}, {{$one->deliveryAddress->locality}}, {{$one->deliveryAddress->streetAddress}}
                        @else
                            Відсутнє
                        @endif
                    </td>
                    <td>
                        @if(!empty($one->deliveryDate->endDate) || !empty($one->deliveryDate->startDate))
                            @if(!empty($one->deliveryDate->startDate)) від {{date('d.m.Y', strtotime($one->deliveryDate->startDate))}}<br>@endif
                            @if(!empty($one->deliveryDate->endDate)) до {{date('d.m.Y', strtotime($one->deliveryDate->endDate))}}@endif
                        @elseif(!empty($one->deliveryDate))
                            {{date('d.m.Y', strtotime($one->deliveryDate))}}
                        @else
                            Відсутній
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <br>
    @endif

    <?php
        if(!empty($lot->__bids))
            $bids=$lot->__bids;
        elseif(!empty($__item->__bids))
            $bids=$__item->__bids;
        else
            $bids=[];
    ?>

    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr valign="top">
            <td width="302">{{$n++}}. Дата оприлюднення оголошення про проведення процедури закупівлі:</td>
            <td>
                <strong>
                    @if(in_array($__item->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdEU', 'aboveThresholdUA.defense']))
                        {{!empty($__item->enquiryPeriod) ? date('d.m.Y H:i', strtotime($__item->enquiryPeriod->startDate)) : 'відсутня'}}
                    @elseif(in_array($__item->procurementMethodType, ['negotiation', 'negotiation.quick']))
                        {{!empty($__item->__active_award->complaintPeriod->startDate) ? date('d.m.Y H:i', strtotime($__item->__active_award->complaintPeriod->startDate)) : 'відсутня'}}
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. Кількість учасників процедури закупівлі:</td>
            <td>
                <strong>
                    @if(in_array($__item->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdEU', 'aboveThresholdUA.defense']))
                        {{sizeof($bids)}}
                    @elseif(in_array($__item->procurementMethodType, ['negotiation', 'negotiation.quick']))
                        {{!empty($lot) ? (int) (!empty($lot->__unique_awards) ? $lot->__unique_awards : 0) : $__item->__unique_awards}}
                    @endif
                </strong>
            </td>
        </tr>
    </table>
    <br>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
        <tr valign="top">
            <td>{{$n++}}. Найменування  учасників  процедури  закупівлі  (для юридичної особи) або прізвище, ім’я, по батькові (для фізичної особи)</td>
            <td>{{$n++}}. Ціна пропозицій учасника до початку аукціону (ціна пропозиції на переговорах у разі застосування переговорної процедури закупівлі)</td>
            <td>{{$n++}}. Ціна пропозицій учасника після закінчення аукціону</td>
            <td>{{$n++}}. Інформація про наявність і відповідність установленим законодавством вимогам документів, що підтверджують відповідність учасників кваліфікаційним критеріям згідно зі статтею 16 Закону України “Про публічні закупівлі”, та наявність/відсутність обставин, установлених статтею 17 цього Закону</td>
        </tr>        
        @if(!empty($bids))
            @foreach($bids as $one)
                @if ($__item->procurementMethodType=='aboveThresholdEU')
                    <?php
                        $q=array_first($__item->qualifications, function($key, $qualification) use ($one){
                            return $qualification->bidID==$one->id;
                        });
                    ?>
                @endif
                @if ($__item->procurementMethodType!='aboveThresholdEU' || ($__item->procurementMethodType=='aboveThresholdEU' && $q->status!='cancelled'))
                    <tr valign="top">
                        <td>
                            <strong>
                                @if($__item->procurementMethod=='open')
                                    @if(!empty($one->tenderers[0]->identifier->legalName))
                                        {{$one->tenderers[0]->identifier->legalName}}<br>
                                    @elseif(!empty($one->tenderers[0]->name))
                                        {{$one->tenderers[0]->name}}<br>
                                    @endif
                                @elseif($__item->procurementMethod=='limited')
                                    @if(!empty($one->suppliers[0]->identifier->legalName))
                                        {{$one->suppliers[0]->identifier->legalName}}<br>
                                    @elseif(!empty($one->suppliers[0]->name))
                                        {{$one->suppliers[0]->name}}<br>
                                    @endif
                                @endif
                            </strong>
                        </td>
                        <td>
                            <strong>
                                @if($__item->procurementMethod=='open')
                                    @if(!empty($__item->__initial_bids_by_lot[$lot->id][$one->id]))
                                        {{str_replace('.00', '', number_format($__item->__initial_bids_by_lot[$lot->id][$one->id], 2, '.', ' '))}}
                                        {{$one->value->currency}}{{$one->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                                    @elseif(!empty($__item->__initial_bids[$one->id]))
                                        {{str_replace('.00', '', number_format($__item->__initial_bids[$one->id], 2, '.', ' '))}}
                                        {{$one->value->currency}}{{$one->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                                    @elseif(!empty($one->value))
                                        {{str_replace('.00', '', number_format($one->value->amount, 2, '.', ' '))}}  {{$one->value->currency}}{{$one->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                                    @elseif(!empty($one->bids_values[$k]->value))
                                        {{str_replace('.00', '', number_format($one->bids_values[$k]->value->amount, 2, '.', ' '))}} {{$one->bids_values[$k]->value->currency}}{{$one->bids_values[$k]->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                                    @else
                                        — 
                                    @endif
                                @elseif($__item->procurementMethod=='limited')
                                    {{str_replace('.00', '', number_format($one->value->amount, 2, '.', ' '))}} {{$one->value->currency}}{{$one->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                                @endif
                            </strong>
                        </td>
                        <td>
                            <strong>
                                @if(!empty($one->value))
                                    {{str_replace('.00', '', number_format($one->value->amount, 2, '.', ' '))}} {{$one->value->currency}}{{$one->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                                @elseif(!empty($lot->bids_values[$k]->value))
                                    {{str_replace('.00', '', number_format($one->bids_values[$k]->value->amount, 2, '.', ' '))}} {{$one->bids_values[$k]->value->currency}}{{$one->bids_values[$k]->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                                @endif
                            </strong>
                        </td>
                        <td>
                            <strong>
                                @if ($__item->procurementMethodType=='aboveThresholdEU')
                                    @if(!empty($q))
                                        @if($q->status=='active')
                                            Відповідає кваліфікаційним критеріям, встановленим в тендерній документації. Відсутні підстави для відмови, установлені  ст. 17 Закону України ”Про публічні закупівлі”
                                        @elseif($q->status=='unsuccessful')
                                            {{$q->title}}@if(!empty($q->description))<br>{{$q->description}}@endif
                                        @elseif($q->status=='pending')
                                            Не розглядався
                                        @elseif($q->status=='cancelled')
                                            не виводити
                                        @endif
                                    @endif
                                @else
                                    @if(!empty($one->__award)){{--it's award--}}
                                        @if(!empty($one->__award->qualified) && !empty($one->__award->eligible))
                                            Відповідає кваліфікаційним критеріям, встановленим в тендерній документації. Відсутні підстави для відмови, установлені  ст. 17 Закону України ”Про публічні закупівлі”
                                        @endif
                                        @if($one->__award->status=='unsuccessful')
                                            @if(!empty($one->__award->title))
                                                {{$one->__award->title}}<br>
                                            @endif
                                            @if(!empty($one->__award->description))
                                                {!! nl2br($one->__award->description) !!}
                                            @endif
                                        @elseif($one->__award->status=='pending')
                                            Не розглядався
                                        @endif
                                    @else
                                        Не розглядався
                                    @endif
                                @endif
                            </strong>
                        </td>
                    </tr>
                @endif
            @endforeach
        @endif        
    </table>
    <br>

    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr valign="top">
            <td width="302">{{$n++}}. Дата оприлюднення повідомлення про намір укласти договір:</td>
            <td>
                @if (empty($lot->__active_award) && !empty($__item->lots) && sizeof($__item->lots)==1)
                    @if (!empty($__item->__active_award->__date))
                        <strong>
                            {{$__item->__active_award->__date}}
                        </strong>
                    @endif
                @else                
                    @if (!empty($lot->__active_award->__date))
                        <strong>
                            {{$lot->__active_award->__date}}
                        </strong>
                    @endif
                @endif
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. Підстави для прийняття рішення про неукладення договору про закупівлю (у разі якщо в результаті проведення торгів не було укладено договір про закупівлю):</td>
            <td>
                @if(in_array($item->status, ['cancelled', 'unsuccessful']))
                    @if($lot->status=='cancelled' && !empty($lot->__cancellations))
                        @foreach($lot->__cancellations as $cancellation)
                            @if(!empty($cancellation->reason))
                                <div>{{$cancellation->reason}}</div>
                            @endif
                        @endforeach
                    @elseif(in_array($item->procurementMethodType, ['aboveThresholdEU', 'aboveThresholdUA', 'aboveThresholdUA.defense', 'competitiveDialogueUA.stage2', 'competitiveDialogueEU.stage2']) && !empty($item->__unsuccessful_awards))
                        <div>Відхилення всіх тендерних пропозицій згідно з Законом про публічні закупівлі</div>
                    @else
                        <?php
                            $tenderPeriod=!empty($item->tenderPeriod) ? $item->tenderPeriod : false;
                            $numberOfBids=0;
    
                            if(!empty($lot->__bids))
                            {
                                $numberOfBids=array_where($lot->__bids, function($key, $bid){
                                    return !empty($bid->status) && $bid->status=='active';
                                });
                            
                                $numberOfBids=$numberOfBids ? sizeof($numberOfBids) : 0;
                            }

                            $numberOfQualifications=0;
    
                            if(!empty($lot->__qualifications))
                            {
                                $numberOfQualifications=array_where($lot->__qualifications, function($key, $qualification){
                                    return !empty($qualification->status) && $qualification->status=='active';
                                });
                                
                                $numberOfQualifications=$numberOfQualifications ? sizeof($numberOfQualifications) : 0;
                            }
                        ?>
                        @if($lot->status=='unsuccessful')
                            @if(in_array($item->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdUA.defense']))
                                <div>подання для участі в торгах менше двох тендерних пропозицій</div>
                            @elseif(in_array($item->procurementMethodType, ['competitiveDialogueUA', 'competitiveDialogueEU']))
                                <div>подання для участі в торгах менше трьох тендерних пропозицій</div>
                            @elseif($item->procurementMethodType=='belowThresholdUA')
                                <div>відсутність тендерних пропозицій</div>
                            @elseif(in_array($item->procurementMethodType, ['aboveThresholdEU', 'competitiveDialogueEU.stage2']) && $numberOfBids < 2)
                                <div>подання для участі в торгах менше двох тендерних пропозицій</div>
                            @elseif($item->procurementMethodType=='aboveThresholdEU' && $numberOfQualifications < 2)
                                <div>допущення до оцінки менше двох тендерних пропозицій</div>
                            @elseif(in_array($item->procurementMethodType, ['competitiveDialogueEU', 'competitiveDialogueUA']) && $numberOfQualifications < 3)
                                <div>допущення до переговорів менше трьох тендерних пропозицій</div>
                            @else
                                <div>відсутність пропозицій</div>
                            @endif
                        @endif
                    @endif
                @else
                    Відсутні
                @endif
            </td>
        </tr>
        <?php
            $contracts=[];

            if(!empty($__item->__signed_contracts) && !empty($__item->lots))
            {
                $contracts=array_where($__item->__signed_contracts, function($key, $contract) use ($lot_id){
                    return !empty($contract->items[0]) && $contract->items[0]->relatedLot==$lot_id;
                });
            }
            elseif(!empty($__item->__signed_contracts))
                $contracts=$__item->__signed_contracts;
        ?>
        <tr valign="top">
            <td width="302">{{$n++}}. Дата укладення договору про закупівлю:</td>
            <td>
                @if(!empty($contracts))
                    <strong>@foreach($contracts as $contract)<div>{{date('d.m.Y', strtotime($contract->dateSigned))}}</div>@endforeach</strong>
                @else
                    Відсутні
                @endif
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. Найменування учасника (для юридичної особи) або прізвище, ім’я, по батькові (для фізичної особи), з яким укладено договір про закупівлю:</td>
            <td>
                @if(!empty($contracts))
                    <strong>
                        @foreach($contracts as $contract)
                            @if(!empty($contract->suppliers[0]->identifier->legalName))
                                {{$contract->suppliers[0]->identifier->legalName}}<br>
                            @elseif(!empty($contract->suppliers[0]->name))
                                {{$contract->suppliers[0]->name}}<br>
                            @endif
                        @endforeach
                    </strong>
                @else
                    Відсутні
                @endif
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. Місцезнаходження учасника, з яким укладено договір про закупівлю:</td>
            <td>
               @if(!empty($contracts))
                    <strong>
                        @foreach($contracts as $contract)
                            @if (!empty($contract->suppliers[0]->address))
                                {{!empty($contract->suppliers[0]->address->postalCode) ? $contract->suppliers[0]->address->postalCode.', ' : ''}}{{!empty($contract->suppliers[0]->address->countryName) ? $contract->suppliers[0]->address->countryName.', ' : '' }}{{!empty($contract->suppliers[0]->address->region) ? $contract->suppliers[0]->address->region.trans('tender.region') : ''}}{{!empty($contract->suppliers[0]->address->locality) ? $contract->suppliers[0]->address->locality.', ' : ''}}{{!empty($contract->suppliers[0]->address->streetAddress) ? $contract->suppliers[0]->address->streetAddress : ''}}
                            @endif
                        @endforeach
                    </strong>
                @else
                    Відсутні
                @endif
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. Інформація про субпідрядника (у разі залучення до виконання робіт або надання послуг):</td>
            <td>
                <?php
                    if(!empty($__item->__active_award) && !empty($__item->bids))
                    {
                        $subcontracting=array_first($__item->bids, function($id, $bid) use ($__item){
                            return $bid->id==$__item->__active_award->bid_id;
                        });
                    }
                ?>
                @if(!empty($subcontracting->subcontractingDetails))
                    {{ $subcontracting->subcontractingDetails }}
                @else
                    <strong>Відсутня</strong>
                @endif
                
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. Сума, визначена в договорі про закупівлю:</td>
            <td>
                @if(!empty($contracts))
                    <strong>
                        @foreach($contracts as $contract)
                            @if (!empty($contract->value->amount))
                                <div>{{str_replace('.00', '', number_format($contract->value->amount, 2, '.', ' '))}}  {{$contract->value->currency}}{{$contract->value->valueAddedTaxIncluded?trans('tender.vat'):''}}</div>
                            @endif
                        @endforeach
                    </strong>
                @else
                    Відсутні
                @endif 
            </td>               
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. Дата оприлюднення оголошення з відомостями про укладену рамкову угоду, за якою укладено договір про закупівлю (у разі проведення закупівлі за рамковими угодами): </td>
            <td><strong>Відсутня</strong></td>
        </tr>
    </table>
@endforeach