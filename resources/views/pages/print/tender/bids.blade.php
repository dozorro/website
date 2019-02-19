@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.registration_form') }}</h2>
        <div>{{ t('tender.received_tenders') }}</div>
    </center>

    <br><br>
    <?php $n=1; ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.number_procurement_procedures_electronic_system') }}:</td>
            <td><strong>{{$item->tenderID}}</strong></td>
        </tr>
    </table>
    <br>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.name_user') }}</td>
            <td>{{$n++}}. {{ t('tender.code_according_EDRPOU') }}</td>
            <td>{{$n++}}. {{ t('tender.date_time_tender_offers') }}</td>
        </tr>
        @if(empty($item->lots) || (!empty($item->lots) && sizeof($item->lots)==1))
            @include('partials/print/bids/bids', [
                'lot'=>$item,
                'lot_id'=>$lot_id,
                '__item'=>$item
            ])
        @else
            @include('partials/print/bids/bids', [
                'lot'=>array_first($item->lots, function($key, $lot) use ($lot_id){
                    return $lot->id==$lot_id;
                }),
                '__item'=>$item,
                'lot_id'=>$lot_id,
                'n'=>$n
            ])
        @endif
    </table>
@endsection