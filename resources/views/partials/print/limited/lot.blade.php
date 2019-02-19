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
                            {{$one->deliveryAddress->countryName}}, {{$one->deliveryAddress->postalCode}}, {{$one->deliveryAddress->region}}, {{$one->deliveryAddress->locality}}, {{$one->deliveryAddress->streetAddress}}
                        @else
                            Відсутнє
                        @endif
                    </td>
                    <td class="small">
                        @if(!empty($one->deliveryDate->endDate) || !empty($one->deliveryDate->startDate))
                            @if(!empty($one->deliveryDate->startDate)) від {{date('d.m.Y', strtotime($one->deliveryDate->startDate))}}<br>@endif
                            @if(!empty($one->deliveryDate->endDate)) до {{date('d.m.Y', strtotime($one->deliveryDate->endDate))}}@endif
                        @elseif(!empty($one->deliveryDate))
                            {{date('d.m.Y', strtotime($one->deliveryDate))}}
                        @else
                            Відсутня
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        <br>
    @endif

    @if(!empty($lot->awards) || !empty($item->awards))
        <br>
        <center>Інформація про учасника (учасників)</center>
        <br>
        <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
            <tr valign="top">
                <td>{{$n++}}. Найменування учасника (учасників) (для юридичної особи) або прізвище, ім’я, по батькові (для фізичної особи), з яким (якими) проведено переговори</td>
                <td>{{$n++}}. Код згідно з ЄДРПОУ/реєстраційний  номер облікової картки платника податків учасника (учасників), з яким (якими) проведено переговори</td>
                <td>{{$n++}}. Місцезнаходження (для юридичної особи) або місце проживання (для фізичної особи) учасника (учасників), з яким (якими) проведено переговори, телефон</td>
                <td>{{$n++}}. Ціна пропозиції</td>
            </tr>
            <?php
                $awards=(!empty($lot->awards) ? $lot->awards : $item->awards);

                $awards=array_where($awards, function($key, $award){
                    return in_array($award->status, ['unsuccessful', 'active']);
                });
            ?>
            @foreach($awards as $award)
                <tr valign="top">
                    <td>
                        @if(!empty($award->suppliers[0]->identifier->legalName))
                            {{$award->suppliers[0]->identifier->legalName}}
                        @elseif(!empty($award->suppliers[0]->name))
                            {{$award->suppliers[0]->name}}
                        @endif
                    </td>
                    <td>
                        {{$award->suppliers[0]->identifier->id}}
                    </td>
                    <td>
                        @if(!empty($award->suppliers[0]->address->streetAddress))
                            {{!empty($award->suppliers[0]->address->postalCode) ? $award->suppliers[0]->address->postalCode : ''}}{{!empty($award->suppliers[0]->address->countryName) ? ', '.$award->suppliers[0]->address->countryName : ''}}{{!empty($award->suppliers[0]->address->region) ? ', '.$award->suppliers[0]->address->region : ''}}{{!empty($award->suppliers[0]->address->locality) ? ', '.$award->suppliers[0]->address->locality : ''}}, {{$award->suppliers[0]->address->streetAddress}}
                            <br><br>
                            @if(!empty($award->suppliers[0]->contactPoint->telephone))
                                {{$award->suppliers[0]->contactPoint->telephone}}
                            @endif
                        @else
                            Відсутнє
                        @endif
                    </td>
                    <td>
                        {{str_replace('.00', '', number_format($award->value->amount, 2, '.', ' '))}} {{$award->value->currency}}{{$award->value->valueAddedTaxIncluded?trans('tender.vat'):''}}
                    </td>
                </tr>
            @endforeach
        </table>
        <br>
    @endif

    @if(in_array($item->procurementMethodType, ['negotiation', 'negotiation.quick']))
        @if (!empty($item->cause))
            <div style="margin-bottom:5px">{{$n++}}. Умова застосування переговорної процедури закупівлі відповідно до частини другої статті 35 Закону України “Про публічні закупівлі”</div>
            <div>{{trans('negotiation.'.$item->cause)}}</div>
            <br>
        @endif
        @if (!empty($item->causeDescription))
            <div style="margin-bottom:5px">{{$n++}}. Обґрунтування застосування переговорної процедури закупівлі</div>
            <div>{!!nl2br($item->causeDescription)!!}</div>
        @endif
    @endif

@endforeach