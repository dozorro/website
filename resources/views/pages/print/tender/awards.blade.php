@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.protocol_form.title') }}</h2>
        <div>{{ t('tender.opening_tenders') }}</div>
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
            <td width="302">{{$n++}}. {{ t('tender.code_according_customer_EDRPOU') }}:</td>
            @if (!empty($item->procuringEntity->identifier->id))
                <td><strong>{{$item->procuringEntity->identifier->id}}</strong></td>
            @endif
        </tr>
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.location_customer') }}:</td>
            @if (!empty($item->procuringEntity->address))
                <td><strong>{{!empty($item->procuringEntity->address->postalCode) ? $item->procuringEntity->address->postalCode.', ' : ''}}{{!empty($item->procuringEntity->address->countryName) ? $item->procuringEntity->address->countryName.', ' : '' }}{{!empty($item->procuringEntity->address->region) ? $item->procuringEntity->address->region.trans('tender.region') : ''}}{{!empty($item->procuringEntity->address->locality) ? $item->procuringEntity->address->locality.', ' : ''}}{{!empty($item->procuringEntity->address->streetAddress) ? $item->procuringEntity->address->streetAddress : ''}}</strong></td>
            @endif
        </tr>
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.contact_person_customer') }}:</td>
            @if(!empty($item->procuringEntity->contactPoint->name))
                <td><strong>{{$item->procuringEntity->contactPoint->name}}</strong></td>
            @endif
        </tr>
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.number_procurement_procedures_electronic_procurement_system') }}:</td>
            <td><strong>{{$item->tenderID}}</strong></td>
        </tr>
    </table>

    @if(empty($item->lots))
        @include('partials/print/awards/items', [
            'lot'=>$item,
            '__item'=>$item,
            'lot_id'=>$lot_id,
            'n'=>$n
        ])
    @else
        @include('partials/print/awards/items', [
            'lot'=>array_first($item->lots, function($key, $lot) use ($lot_id){
                return $lot->id==$lot_id;
            }),
            '__item'=>$item,
            'lot_id'=>$lot_id,
            'n'=>$n
        ])
    @endif

@endsection