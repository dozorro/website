@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.message') }}</h2>
        <div>{{ t('tender.intention_conclude_agreement') }}<br>({{ t('tender.in_applying_negotiation_procedure') }})<br>{{$item->tenderID}}</div>
    </center>

    <br><br>
    <?php
        $n=1;
    ?>
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
            <td>{{$n++}}. {{ t('tender.search.code_according_customer_EDRPOU') }}:</td>
            @if (!empty($item->procuringEntity->identifier->id))
                <td><strong>{{$item->procuringEntity->identifier->id}}</strong></td>
            @endif
        </tr>
        <tr>
            <td>{{$n++}}. {{ t('tender.location_customer') }}Місцезнаходження замовника:</td>
            @if (!empty($item->procuringEntity->address))
                <td><strong>{{!empty($item->procuringEntity->address->postalCode) ? $item->procuringEntity->address->postalCode.', ' : ''}}{{!empty($item->procuringEntity->address->countryName) ? $item->procuringEntity->address->countryName.', ' : '' }}{{!empty($item->procuringEntity->address->region) ? $item->procuringEntity->address->region.t('tender.region') : ''}}{{!empty($item->procuringEntity->address->locality) ? $item->procuringEntity->address->locality.', ' : ''}}{{!empty($item->procuringEntity->address->streetAddress) ? $item->procuringEntity->address->streetAddress : ''}}</strong></td>
            @endif
        </tr>
    </table>

    @if(empty($item->lots))
        @include('partials/print/limited/lot', [
            'lots'=>[$item],
            '__item'=>$item
        ])
    @else
        @include('partials/print/limited/lot', [
            'lots'=>$item->lots,
            '__item'=>$item
        ])
    @endif
@endsection