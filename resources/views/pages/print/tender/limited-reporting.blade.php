@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.report.title') }}</h2>
        <div>{{ t('tender.of_contract') }}<br>{{$item->tenderID}}</div>
    </center>

    <br><br>
    <?php
        $n=1;
        $contract=head($item->contracts);
    ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.date_contract') }}:</td>
            <td><strong>{{!empty($contract->dateSigned) ? date('d.m.Y H:i', strtotime($contract->dateSigned)) : 'відсутня'}}</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.contract_number') }}:</td>
            <td><strong>{{!empty($contract->contractNumber) ? $contract->contractNumber : 'відсутній'}}</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.name_customer') }}</td>
            <td>
                <strong>
                    @if (!empty($item->procuringEntity->identifier->legalName))
                        {{$item->procuringEntity->identifier->legalName}}
                    @elseif (!empty($item->procuringEntity->name))
                        {{$item->procuringEntity->name}}
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.code_according_to_customer_EDRPOU') }}:</td>
            <td><strong>{{$item->procuringEntity->identifier->id}}</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.location_customer') }}:</td>
            <td>
                <strong>
                    @if (!empty($item->procuringEntity->address))
                        {{!empty($item->procuringEntity->address->postalCode) ? $item->procuringEntity->address->postalCode.', ' : ''}}{{!empty($item->procuringEntity->address->countryName) ? $item->procuringEntity->address->countryName.', ' : '' }}{{!empty($item->procuringEntity->address->region) ? $item->procuringEntity->address->region.t('tender.region') : ''}}{{!empty($item->procuringEntity->address->locality) ? $item->procuringEntity->address->locality.', ' : ''}}{{!empty($item->procuringEntity->address->streetAddress) ? $item->procuringEntity->address->streetAddress : ''}}
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.name_supplier_goods_contractor_or_service_provider') }}:</td>
            <td><strong>{{!empty($contract->suppliers[0]->identifier->legalName) ? $contract->suppliers[0]->identifier->legalName : $contract->suppliers[0]->name}}</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.code_according _EDRPOU_or_registration_number_taxpayer_registration_card') }}:</td>
            <td><strong>{{$contract->suppliers[0]->identifier->id}}</strong></td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.location_provider_goods_contractor_or_service_provider') }}:</td>
            <td>
                <strong>
                    @if (!empty($contract->suppliers[0]->address))
                        {{!empty($contract->suppliers[0]->address->postalCode) ? $contract->suppliers[0]->address->postalCode.', ' : ''}}{{!empty($contract->suppliers[0]->address->countryName) ? $contract->suppliers[0]->address->countryName.', ' : '' }}{{!empty($contract->suppliers[0]->address->region) ? $contract->suppliers[0]->address->region.t('tender.region') : ''}}{{!empty($contract->suppliers[0]->address->locality) ? $contract->suppliers[0]->address->locality.', ' : ''}}{{!empty($contract->suppliers[0]->address->streetAddress) ? $contract->suppliers[0]->address->streetAddress : ''}}
                    @endif

                    @if (!empty($contract->suppliers[0]->contactPoint->telephone))
                        <br>{{$contract->suppliers[0]->contactPoint->telephone}}
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.exact_name_procurement') }}:</td>
            <td><strong>{{$item->title}}</strong></td>
        </tr>
    </table>

    @if(!empty($item->awards))
        <br>
        <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
            <tr valign="top">
                <td>{{$n++}}. {{ t('tender.name_goods_works_services') }}</td>
                <td>{{$n++}}. {{ t('tender.number_goods_works_services') }}</td>
                <td>{{$n++}}. {{ t('tender.place_delivery_goods_works_services') }}</td>
                <td>{{$n++}}. {{ t('tender.delivery_goods_works_services') }}</td>
            </tr>
            @foreach($item->items as $one)
                <tr valign="top">
                    <td>
                        {{$one->description}}
                    </td>
                    <td>
                        {{!empty($one->quantity)?$one->quantity:''}} @if(!empty($one->unit->code)){{t('measures.'.$one->unit->code.'.symbol')}}@endif
                    </td>
                    <td>
                        @if(!empty($one->deliveryAddress->streetAddress))
                            {{$one->deliveryAddress->countryName}}, {{$one->deliveryAddress->postalCode}}, {{$one->deliveryAddress->region}}, {{$one->deliveryAddress->locality}}, {{$one->deliveryAddress->streetAddress}}
                        @else
                            <!--Відсутнє-->
                            {{ t('tender.none2') }}
                        @endif
                    </td>
                    <td>
                        {{date('d.m.Y H:i', strtotime($one->deliveryDate->endDate))}}
                    </td>
                </tr>
            @endforeach
        </table>
        <br>
    @endif

    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.information_about_technical_and_quality_characteristics_goods_works_services') }}:</td>
            <td><strong></strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.price_agreement') }}:</td>
            <td><strong>{{str_replace('.00', '', number_format($contract->value->amount, 2, '.', ' '))}} {{$contract->value->currency}}{{$contract->value->valueAddedTaxIncluded?t('tender.vat'):''}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.contract_term') }}:</td>
            <td>
                <strong>
                    @if(!empty($contract->period->endDate) || !empty($contract->period->startDate))
                        @if(!empty($contract->period->startDate)) від {{date('d.m.Y H:i', strtotime($contract->period->startDate))}}<br>@endif
                        @if(!empty($contract->period->endDate)) до {{date('d.m.Y H:i', strtotime($contract->period->endDate))}}@endif
                    @elseif(!empty($contract->period))
                        {{date('d.m.Y H:i', strtotime($contract->period))}}
                    @else
                        <!--Відсутня-->
                        {{ t('tender.none3') }}
                    @endif
                </strong>
            </td>
        </tr>
    </table>
@endsection