<?php
    $start_n=$n;
?>

<br>
@foreach($lots as $k=>$lot)

    @if(!empty($lot->title) && sizeof($lots)>1)
        <center>
            <h3>ЛОТ {{$k+1}} — {{$lot->title}}</h3>
            @if ($eng && !empty($lot->title_en))
                <div><strong>Title: {{$lot->title_en}}</strong></div>
                <br><br>
            @endif
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
                        <div>{{$one->description}}</div>
                        @if($eng && !empty($one->description_en))
                            <div>Description: {{$one->description_en}}</div>
                        @endif
                    </td>
                    <td>
                        @if (!empty($one->classification))
                            <div>{{trans('tender.cpv')}}: {{$one->classification->id}} — {{$one->classification->description}}</div>
                            @if($eng)
                                <div>CPV: {{$one->classification->id}}</div>
                            @endif
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
    
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr valign="top">
            <td width="302">{{$n++}}. Розмір  бюджетного  призначення  за  кошторисом  або  очікувана вартість  предмета закупівлі:</td>
            @if(!empty($lot->value))
                <td><strong>{{str_replace('.00', '', number_format($lot->value->amount, 2, '.', ' '))}} {{$lot->value->currency}}</strong></td>
            @endif
        </tr>
        <tr valign="top">
            <td>{{$n++}}. Розмір мінімального кроку пониження ціни:</td>
            @if(!empty($lot->minimalStep))
                <td><strong>{{str_replace('.00', '', number_format($lot->minimalStep->amount, 2, '.', ' '))}} {{$lot->minimalStep->currency}}</strong></td>
            @endif
        </tr>
        <tr valign="top">
            <td>{{$n++}}. Математична формула, яка буде застосовуватися при проведенні електронного аукціону для визначення показників інших критеріїв оцінки:</td>
            <td>
                <strong>
                    @if(!empty($__item->features))
                        PP = P/(1 + (F1 + F2 +… + Fn)/PV), де:<br>
                        <div style="margin-left:10px">
                            PP - приведена ціна;<br>
                            P - ціна;<br>
                            F1…Fn - питома вага інших критеріїв оцінки,<br>запропонованих учасником;<br>
                            PV - питома вага критерію “ціна”
                        </div>
                    @else
                        відсутня
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. Кінцевий строк подання тендерних пропозицій:</td>
            <td><strong>{{!empty($__item->tenderPeriod) ? date('d.m.Y H:i', strtotime($__item->tenderPeriod->endDate)) : 'відсутній'}}</strong></td>
        </tr>
        @if (!empty($__item->lots))
            <?php $guarantee=(!empty($lot->guarantee) ? $lot->guarantee : false); ?>
        @else
            <?php $guarantee=(!empty($__item->guarantee) ? $__item->guarantee : false); ?>
        @endif
        
        <tr valign="top">
            <td>{{$n++}}. Розмір забезпечення тендерних пропозиції (якщо замовник вимагає його надати):</td>
            <td><strong>{{!empty($guarantee) ? str_replace('.00', '', number_format($guarantee->amount, 2, '.', ' ')).' '.$guarantee->currency : 'відсутній'}}</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. Вид забезпечення тендерних пропозиції (якщо замовник вимагає його надати):</td>
            <td><strong>{{(!empty($guarantee) && (int)$guarantee->amount>0) ? 'Електронна банківська гарантія' : 'відсутній' }}</strong></td>
        </tr>
        <?php
            $auctionPeriod=false;

            if(!empty($__item->lots) && sizeof($__item->lots)==1 && !empty($__item->lots[0]->auctionPeriod))
                $auctionPeriod=$item->lots[0]->auctionPeriod;
            elseif(!$__item->__isMultiLot && !empty($__item->auctionPeriod))
                $auctionPeriod=$__item->auctionPeriod;
            elseif(!empty($lot->auctionPeriod))
                $auctionPeriod=$lot->auctionPeriod;
        ?>
        <tr valign="top">
            <td>{{$n++}}. Дата та час розкриття тендерних пропозицій</td>
            <td><strong>
                {{!empty($auctionPeriod) ? date('d.m.Y', strtotime($auctionPeriod->startDate)) : 'відсутній'}} 
                @if (in_array($__item->procurementMethodType, ['aboveThresholdUA', 'aboveThresholdUA.defense']))
                    , після завершення електронного аукціону
                @elseif($__item->procurementMethodType=='aboveThresholdUA')
                    , після завершення електронного аукціону
                @endif
            </strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. Дата та час проведення електронного аукціону:</td>
            <td><strong>{{!empty($auctionPeriod) ? date('d.m.Y H:i', strtotime($auctionPeriod->startDate)) : 'відсутній'}}</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. Строк, на який укладається рамкова угода:</td>
            <td><strong>відсутній</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. Кількість учасників, з якими буде укладено рамкову угоду:</td>
            <td><strong>відсутня</strong></td>
        </tr>
    </table>
@endforeach