@extends('layouts/print')

@section('pdf')
    <center>
        <h2>{{ t('tender.message') }}</h2>
        <div>{{ t('tender.amending_agreement') }}</div>
    </center>

    <br><br>
    <?php
        $n=1;
        $tender=$item;
        
        if(!empty($item->lots))
        {
            $item=array_first($item->lots, function($key, $lot) use ($lot_id){
                return $lot->id==$lot_id;
            });
        }

        if(!property_exists($item, '__contracts_changes'))
            $item=$tender;

        $contract=array_first($item->__contracts_changes, function($key, $document){
            return $document->id==$_GET['contract'];
        });

    ?>
    <table cellpadding="5" cellspacing="0" border="0" width="100%">
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
                        {{!empty($tender->procuringEntity->address->postalCode) ? $tender->procuringEntity->address->postalCode.', ' : ''}}{{!empty($tender->procuringEntity->address->countryName) ? $tender->procuringEntity->address->countryName.', ' : '' }}{{!empty($tender->procuringEntity->address->region) ? $tender->procuringEntity->address->region.trans('tender.region') : ''}}{{!empty($tender->procuringEntity->address->locality) ? $tender->procuringEntity->address->locality.', ' : ''}}{{!empty($tender->procuringEntity->address->streetAddress) ? $tender->procuringEntity->address->streetAddress : ''}}
                    @endif
                </strong>
            </td>
        </tr>        
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.number_procurement_procedures_electronic_system') }}:</td>
            <td><strong>{{$tender->tenderID}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.number_purchase_agreement') }}:</td>
            <td><strong>{{!empty($contract->contractNumber) ? $contract->contractNumber : 'не вказано'}}</strong></td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.date_contract') }}:</td>
            <td><strong>{{date('d.m.Y H:i', strtotime($item->__contract_ongoing->dateSigned))}}</strong></td>
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
                        {{!empty($item->__active_award->suppliers[0]->address->postalCode) ? $item->__active_award->suppliers[0]->address->postalCode.', ' : ''}}{{!empty($item->__active_award->suppliers[0]->address->countryName) ? $item->__active_award->suppliers[0]->address->countryName.', ' : '' }}{{!empty($item->__active_award->suppliers[0]->address->region) ? $item->__active_award->suppliers[0]->address->region.trans('tender.region') : ''}}{{!empty($item->__active_award->suppliers[0]->address->locality) ? $item->__active_award->suppliers[0]->address->locality.', ' : ''}}{{!empty($item->__active_award->suppliers[0]->address->streetAddress) ? $item->__active_award->suppliers[0]->address->streetAddress : ''}}
                    @endif
                </strong>
            </td>
        </tr>
        <tr valign="top">
            <td width="302">{{$n++}}. {{ t('tender.date_amending_agreement') }}:</td>
            <td><strong>{{date('d.m.Y H:i', strtotime($contract->date))}}</strong></td>
        </tr>
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.cases_changes_essential_terms_contract') }}:</td>
            <td><strong>{!!implode('<br>', $contract->rationaleTypes)!!}</strong></td>
        </tr>
        <tr>
            <td width="302">{{$n++}}. {{ t('tender.description_change') }}:</td>
            <td>
                <strong>{{!empty($contract->rationale) ? $contract->rationale : 'відсутні'}}</strong>
            </td>
        </tr>
    </table>
@endsection