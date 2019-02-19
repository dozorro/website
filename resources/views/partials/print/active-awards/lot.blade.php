<?php $start_n=$n; ?>

<br>
@foreach($lots as $k=>$lot)
    @if($lot->id==$lot_id)
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
                        <td class="small">
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
            $__active_award=!empty($lot->__active_award) ? $lot->__active_award : $item->__active_award;
        ?>
        @if(!empty($__active_award))
            <table cellpadding="5" cellspacing="0" border="0" width="100%">
                <tr valign="top">
                    <td width="302">{{$n++}}. Найменування учасника − переможця процедури закупівлі (для юридичної особи) або прізвище, ім’я, по батькові (для фізичної особи):</td>
                    <td><strong>
                        @if(!empty($__active_award->suppliers[0]->identifier->legalName))
                            {{$__active_award->suppliers[0]->identifier->legalName}}
                        @else
                            {{$__active_award->suppliers[0]->identifier->name}}
                        @endif
                    </strong></td>
                </tr>
                <tr valign="top">
                    <td>{{$n++}}.Інформація про ціну пропозиції:</td>
                    <td><strong>{{str_replace('.00', '', number_format($__active_award->value->amount, 2, '.', ' '))}} {{$__active_award->value->currency}}</strong></td>
                </tr>
            </table>
        @endif
    @endif        
@endforeach