@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.report.title') }}</h2>
        <div>{{ t('tender.of_tender_results') }}<br>{{$item->tenderID}}</div>
        @if (!empty($lot_id))
            <div><strong>{{array_first($item->lots, function($key, $lot) use ($lot_id){
                return $lot->id==$lot_id;
            })->title}}</strong></div>
        @endif
        <?php
            $active_contract=false;

            if(empty($item->lots))
            {
                if(!empty($item->contracts))
                {
                    $active_contract=array_first($item->contracts, function($key, $contract){
                        return $contract->status=='active';
                    });

                    $active_contract=array_first($active_contract->documents, function($key, $contract){
                        return $contract->title=='sign.p7s';
                    });
                }
            }
            elseif(!empty($item->lots) && sizeof($item->lots)==1)
            {
                if(!empty($item->__documents))
                {
                    $active_contract=array_first($item->__documents, function($key, $contract){
                        return $contract->title=='sign.p7s';
                    });
                }
            }
            else
            {
                $current_lot=array_first($item->lots, function($key, $lot) use ($lot_id){
                    return $lot->id==$lot_id;
                });

                if(!empty($current_lot->__documents))
                {
                    $active_contract=array_first($current_lot->__documents, function($key, $contract){
                        return $contract->title=='sign.p7s';
                    });
                }
            }
        ?>
        @if (!empty($current_lot->date))
            <div>{{ t('tender.date_report_generation') }}: {{date('d.m.Y', strtotime($current_lot->date))}}</div>
        @elseif (!empty($item->date))
            <div>{{ t('tender.date_report_generation') }}: {{date('d.m.Y', strtotime($item->date))}}</div>
        @elseif (!empty($active_contract->datePublished))
            <div>{{ t('tender.date_report_generation') }}: {{date('d.m.Y', strtotime($active_contract->datePublished))}}</div>
        @endif
    </center>

    <br><br>
    <?php $n=1; ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.name_customer') }}:</td>
            @if (!empty($item->procuringEntity->identifier->legalName))
                <td><strong>{{$item->procuringEntity->identifier->legalName}}</strong></td>
            @elseif (!empty($item->procuringEntity->name))
                <td><strong>{{$item->procuringEntity->name}}</strong></td>
            @endif
        </tr>
        <tr>
            <td>{{$n++}}. {{ t('tender.code_according_customer_EDRPOU') }}:</td>
            @if (!empty($item->procuringEntity->identifier->id))
                <td><strong>{{$item->procuringEntity->identifier->id}}</strong></td>
            @endif
        </tr>
    </table>
    @if(empty($item->lots))
        @include('partials/print/report/lot', [
            'lots'=>[$item],
            '__item'=>$item
        ])
    @elseif(!empty($item->lots) && sizeof($item->lots)==1)
        @include('partials/print/report/lot', [
            'lots'=>[$item->lots[0]],
            '__item'=>$item
        ])
    @else
        @include('partials/print/report/lot', [
            'lots'=>array_where($item->lots, function($key, $lot) use ($lot_id){
                return $lot->id==$lot_id;
            }),
            '__item'=>$item
        ])
    @endif

@endsection