@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.report.title') }}</h2>
        <div>{{ t('tender.implementation_procurement_contract') }}</div>
    </center>

    <br><br>
    <?php
        $n=1;
        $tender=$item;
        
        if(!empty($item->lots) && sizeof($item->lots)>1)
        {
            $item=array_first($item->lots, function($key, $lot) use ($lot_id){
                return $lot->id==$lot_id;
            });
        }

    ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.number_procurement_procedures_electronic_system') }}:</td>
            <td><strong>{{$tender->tenderID}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.number_purchase_agreement') }}:</td>
            <td><strong>{{!empty($item->__contract_ongoing->contractNumber) ? $item->__contract_ongoing->contractNumber : t('tender.none') }}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.date_contract') }}:</td>
            <td><strong>{{date('d.m.Y H:i', strtotime($item->__contract_ongoing->dateSigned))}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.price_purchase_agreement') }}:</td>
            <td><strong>{{str_replace('.00', '', number_format($item->__contract_ongoing->amountPaid->amount, 2, '.', ' '))}} {{$item->__contract_ongoing->amountPaid->currency}}{{$item->__contract_ongoing->amountPaid->valueAddedTaxIncluded?t('tender.vat'):''}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.name_customer') }}:</td>
            <td>
                <strong>
                    @if(!empty($tender->procuringEntity->identifier->legalName))
                        {{$tender->procuringEntity->identifier->legalName}}
                    @else
                        {{$tender->procuringEntity->identifier->name}}
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.code_according_customer_EDRPOU') }}:</td>
            <td><strong>{{$tender->procuringEntity->identifier->id}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.location_customer') }}:</td>
            <td>
                <strong>
                    @if (!empty($tender->procuringEntity->address))
                        {{!empty($tender->procuringEntity->address->postalCode) ? $tender->procuringEntity->address->postalCode.', ' : ''}}{{!empty($tender->procuringEntity->address->countryName) ? $tender->procuringEntity->address->countryName.', ' : '' }}{{!empty($tender->procuringEntity->address->region) ? $tender->procuringEntity->address->region.t('tender.region') : ''}}{{!empty($tender->procuringEntity->address->locality) ? $tender->procuringEntity->address->locality.', ' : ''}}{{!empty($tender->procuringEntity->address->streetAddress) ? $tender->procuringEntity->address->streetAddress : ''}}
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.name_participant_signed_procurement_contract') }}:</td>
            <td><strong>{{!empty($item->__active_award->suppliers[0]->identifier->legalName) ? $item->__active_award->suppliers[0]->identifier->legalName : $item->__active_award->suppliers[0]->name}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.registration_number_taxpayer_registration_card_party') }}:</td>
            <td><strong>{{$item->__active_award->suppliers[0]->identifier->id}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.location_or_place_residence') }}:</td>
            <td>
                <strong>
                    @if (!empty($item->__active_award->suppliers[0]->address))
                        {{!empty($item->__active_award->suppliers[0]->address->postalCode) ? $item->__active_award->suppliers[0]->address->postalCode.', ' : ''}}{{!empty($item->__active_award->suppliers[0]->address->countryName) ? $item->__active_award->suppliers[0]->address->countryName.', ' : '' }}{{!empty($item->__active_award->suppliers[0]->address->region) ? $item->__active_award->suppliers[0]->address->region.t('tender.region') : ''}}{{!empty($item->__active_award->suppliers[0]->address->locality) ? $item->__active_award->suppliers[0]->address->locality.', ' : ''}}{{!empty($item->__active_award->suppliers[0]->address->streetAddress) ? $item->__active_award->suppliers[0]->address->streetAddress : ''}}
                    @endif
                </strong>
            </td>
        </tr>
    </table>
    <br>
    <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
        <tr valign="top">
            <td>{{$n++}}. {{ t('tender.exact_name_procurement') }}</td>
            <td>{{$n++}}. {{ t('tender.item_number_volume_work_or_services_under_contract') }}</td>
            <td>{{$n++}}. {{ t('tender.place_delivery_goods_works_services') }}</td>
            <td>{{$n++}}. {{ t('tender.delivery_goods_works_services_under_contract') }}</td>
        </tr>
        @foreach((!empty($item->__items) ? $item->__items : $item->items) as $one)
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
                <td class="small">
                    @if(!empty($one->deliveryDate->endDate) || !empty($one->deliveryDate->startDate))
                        @if(!empty($one->deliveryDate->startDate)) від {{date('d.m.Y', strtotime($one->deliveryDate->startDate))}}<br>@endif
                        @if(!empty($one->deliveryDate->endDate)) до {{date('d.m.Y', strtotime($one->deliveryDate->endDate))}}@endif
                    @elseif(!empty($one->deliveryDate))
                        {{date('d.m.Y', strtotime($one->deliveryDate))}}
                    @else
                        <!--Відсутня-->
                        {{ t('tender.none3') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <br>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.contract_term') }}:</td>
            <td><strong>{{date('d.m.Y', strtotime($item->__contract_ongoing->period->startDate))}} — {{date('d.m.Y', strtotime($item->__contract_ongoing->dateModified))}}</strong></td>
        </tr>
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.total_payments_under_agreement') }}:</td>
            <td>
                <strong>
                    {{str_replace('.00', '', number_format($item->__contract_ongoing->amountPaid->amount, 2, '.', ' '))}} {{$item->__contract_ongoing->amountPaid->currency}}{{$item->__contract_ongoing->amountPaid->valueAddedTaxIncluded?t('tender.vat'):''}}
                </strong>
            </td>
        </tr>
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.reasons_termination_contract') }}:</td>
            <td valign="top">
                <strong>
                    {{!empty($item->__contract_ongoing->terminationDetails) ? $item->__contract_ongoing->terminationDetails : 'відсутні'}}
                </strong>
            </td>
        </tr>
        
    </table>
@endsection