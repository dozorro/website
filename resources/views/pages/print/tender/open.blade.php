@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.advertisement.title') }}</h2>
        <div>{{ t('tender.public_bidding') }}<br>{{$item->tenderID}}</div>
    </center>

    <br><br>
    <?php
        $n=1;
        $eng=in_array($item->procurementMethodType, ['aboveThresholdEU', 'aboveThresholdUA.defense']);
    ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="30">{{$n++}}.</td>
            <td width="272">{{ t('tender.name_customer') }}:</td>
            @if (!empty($item->procuringEntity->identifier->legalName))
                <td><strong>{{$item->procuringEntity->identifier->legalName}}</strong></td>
            @elseif (!empty($item->procuringEntity->name))
                <td><strong>{{$item->procuringEntity->name}}</strong></td>
            @endif
        </tr>
        @if($eng)
            <tr>
                <td></td>
                <td>Purchasing body:</td>
                @if (!empty($item->procuringEntity->identifier->legalName_en))
                    <td><strong>{{$item->procuringEntity->identifier->legalName_en}}</strong></td>
                @elseif (!empty($item->procuringEntity->name_en))
                    <td><strong>{{$item->procuringEntity->name_en}}</strong></td>
                @endif
            </tr>
            <tr>
                <td></td>
                <td>National ID:</td>
                @if (!empty($item->procuringEntity->identifier->id))
                    <td><strong>{{$item->procuringEntity->identifier->id}}</strong></td>
                @endif
            </tr>
        @endif
        <tr>
            <td>{{$n++}}.</td>
            <td>{{ t('tender.code_according_customer_EDRPOU') }}:</td>
            @if (!empty($item->procuringEntity->identifier->id))
                <td><strong>{{$item->procuringEntity->identifier->id}}</strong></td>
            @endif
        </tr>
        <tr>
            <td>{{$n++}}.</td>
            <td>{{ t('tender.location_customer') }}:</td>
            @if (!empty($item->procuringEntity->address))
                <td><strong>{{!empty($item->procuringEntity->address->postalCode) ? $item->procuringEntity->address->postalCode.', ' : ''}}{{!empty($item->procuringEntity->address->countryName) ? $item->procuringEntity->address->countryName.',' : '' }}{{!empty($item->procuringEntity->address->region) ? $item->procuringEntity->address->region.t('tender.region') : ''}}{{!empty($item->procuringEntity->address->locality) ? $item->procuringEntity->address->locality.', ' : ''}}{{!empty($item->procuringEntity->address->streetAddress) ? $item->procuringEntity->address->streetAddress : ''}}</strong></td>
            @endif
        </tr>
        <tr>
            <td>{{$n++}}.</td>
            <td>{{ t('tender.contact_person_customer') }}:</td>
            @if (!empty($item->procuringEntity->contactPoint))
                <td><strong>{{!empty($item->procuringEntity->contactPoint->name) ? $item->procuringEntity->contactPoint->name : ''}}{{!empty($item->procuringEntity->contactPoint->telephone) ? ', '.$item->procuringEntity->contactPoint->telephone : ''}}{{!empty($item->procuringEntity->contactPoint->email) ? ', '.$item->procuringEntity->contactPoint->email : ''}}</strong></td>
            @endif
        </tr>
        @if (!empty($eng) && !empty($item->procuringEntity->contactPoint->name_en))
            <tr>
                <td></td>
                <td>Contact point:</td>
                @if (!empty($item->procuringEntity->contactPoint->name_en))
                    <td><strong>{{$item->procuringEntity->contactPoint->name_en}}</strong></td>
                @endif
            </tr>
        @endif
    </table>

    @if(empty($item->lots))
        @include('partials/print/open/lot', [
            'lots'=>[$item],
            '__item'=>$item,
            'eng'=>$eng
        ])
    @else
        @include('partials/print/open/lot', [
            'lots'=>$item->lots,
            '__item'=>$item,
            'eng'=>$eng
        ])
    @endif

@endsection