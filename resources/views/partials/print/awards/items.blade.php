<?php $start_n=$n; ?>

<table cellpadding="5" cellspacing="0" border="0" width="100%">
    <tr>
        <td width="302">{{$n++}}. Конкретна назва предмета закупівлі:</td>
        <td>
            @if(!empty($lot->__items) || !empty($item->items))
                @foreach((!empty($lot->__items) ? $lot->__items : $item->items) as $one)
                    {!!nl2br($one->description)!!}<br>
                @endforeach
            @else
                Відсутні
            @endif
        </td>
    </tr>
    <tr>
        <td width="302">{{$n++}}. Дата та час розкриття тендерної пропозиції:</td>
        <td><strong>
            @if(!empty($lot->auctionPeriod->endDate))
                {{date('d.m.Y H:i', strtotime($lot->auctionPeriod->endDate))}}
            @else
                Відсутня
            @endif
        </strong></td>
    </tr>
    <tr>
        <td width="302">{{$n++}}. Найменування учасників (для юридичної особи) або прізвище, ім’я, по батькові (для фізичної особи):</td>
        <td></td>
    </tr>
</table>
<table class="table table-striped margin-bottom small-text">
    <thead>
        <tr>
            <th>Найменування учасників (для юридичної особи) або прізвище, ім’я, по батькові (для фізичної особи)</th>
            <th>Інформація та документи, що підтверджують відповідність учасника кваліфікаційним критеріям, а також інформація та документи, що містять технічний опис предмета закупівлі.</th>
            <th>Інформація щодо ціни тендерної пропозиції до початку аукціону</th>
            <th>Інформація щодо ціни тендерної пропозиції після закінчення аукціону</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lot->awards as $award)
            <tr>
                <td>
                    @if(!empty($award->suppliers[0]->identifier->legalName))
                        {{$award->suppliers[0]->identifier->legalName}}<br>
                    @elseif(!empty($award->suppliers[0]->name))
                        {{$award->suppliers[0]->name}}<br>
                    @endif
                    #{{$award->suppliers[0]->identifier->id}}                                                
                </td>
                <td>
                    @if($award->qualified && $award->eligible)
                        Відповідає кваліфікаційним критеріям, встановленим в тендерній документації. Відсутні підстави для відмови, установлені  ст. 17 Закону України ”Про публічні закупівлі
                    @elseif($award->status=='unsuccessful')
                        {{$award->title}}
                        <br>
                        {{$award->description}}
                    @endif
                </td>                                            
                <td>
                    {{str_replace('.00', '', number_format($item->__initial_bids_by_lot[$lot_id][$award->bid_id], 2, '.', ' '))}} 
                    <div class="td-small grey-light">{{$award->value->currency}}{{$award->value->valueAddedTaxIncluded?trans('tender.vat'):''}}</div>
                </td>
                <td>
                    {{str_replace('.00', '', number_format($award->value->amount, 2, '.', ' '))}} 
                    <div class="td-small grey-light">{{$award->value->currency}}{{$award->value->valueAddedTaxIncluded?trans('tender.vat'):''}}</div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>