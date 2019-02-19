@extends('layouts/print')

@section('pdf')
<style type="text/css" media="print">
    @page {
        size:  auto;
        margin: 0mm;
        size: landscape;
    }
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    html{
        background-color: #FFFFFF; 
        margin: 0px;
    }
    body{
        margin:1cm;
    }
</style>
    @if($main)
        <center>
            <h2>{{ t('plan.search.annual_procurement_plan') }}</h2>
            <div>{{ t('plan.search.on') }} {{ $budget->year }} {{ t('plan.search.year') }}</div>
        </center>
        <br><br>
        <?php $n=1; ?>
    
        <p>{{$n++}}. {{ t('plan.search.name_customer') }}: {{ !empty($procuringEntity->identifier->legalName) ? $procuringEntity->identifier->legalName : $procuringEntity->name }}</p>
        <p>{{$n++}}. {{ t('plan.search.code_according_customer_EDRPOU') }}: {{ $procuringEntity->identifier->id }}</p>
    
        <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
            <tr valign="top">
                <td width="30%">{{$n++}}. {{ t('plan.search.exact_name_procurement') }}:</td>
                <td width="20%">{{$n++}}. {{ t('plan.search.codes_relevant_classifications_subject_purchase_when_available') }}</td>
                <td width="10%">{{$n++}}. {{ t('plan.search.code_according_KEKV') }}</td>
                <td width="10%">{{$n++}}. {{ t('plan.search.size_budget_appropriation_estimate_expected_value_purchases') }}</td>
                <td width="5%">{{$n++}}. {{ t('plan.search.purchasing_procedure') }}</td>
                <td width="5%">{{$n++}}. {{ t('plan.search.tentative_start_procurement_procedure') }}</td>
                <td width="20%">{{$n++}}. {{ t('plan.search.remarks') }}</td>
            </tr>
            @include('partials/print/plan/list-table', [
                'items'=>$main
            ])
        </table>
    @endif

    @if($additional)
        <br><br><br>
        <center>
            <h2>{{ t('plan.search.appendix_annual_procurement_plan') }}</h2>
            <div>{{ t('plan.search.on') }} {{ $budget->year }} {{ t('plan.search.year') }}</div>
        </center>
        <br><br>
    
        <?php $n=1; ?>
    
        <p>{{$n++}}. {{ t('plan.search.name_customer') }}: {{ !empty($procuringEntity->identifier->legalName) ? $procuringEntity->identifier->legalName : $procuringEntity->name }}</p>
        <p>{{$n++}}. {{ t('plan.search.code_according_customer_EDRPOU') }}: {{ $procuringEntity->identifier->id }}</p>
    
        <table cellpadding="5" cellspacing="1" border="0" width="100%" class="border">
            <tr valign="top">
                <td width="30%">{{$n++}}. {{ t('plan.search.exact_name_procurement') }}:</td>
                <td width="20%">{{$n++}}. {{ t('plan.search.codes_relevant_classifications_subject_purchase_when_available') }}</td>
                <td width="10%">{{$n++}}. {{ t('plan.search.code_according_KEKV') }}</td>
                <td width="10%">{{$n++}}. {{ t('plan.search.size_budget_appropriation_estimate_expected_value_purchases') }}</td>
                <td width="5%">{{$n++}}. {{ t('plan.search.purchasing_procedure') }}</td>
                <td width="5%">{{$n++}}. {{ t('plan.search.tentative_start_procurement_procedure') }}</td>
                <td width="20%">{{$n++}}. {{ t('plan.search.remarks') }}</td>
            </tr>
            @include('partials/print/plan/list-table', [
                'items'=>$additional
            ])
        </table>
    @endif
@endsection