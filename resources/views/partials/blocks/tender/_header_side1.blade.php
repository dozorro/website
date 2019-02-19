@if(!empty($item->value->amount))
    <div class="tender_info__item">
        <div class="tender_info__label">{{ t('tender.price') }}</div>
        <div class="tender_info__price">{{ str_replace('.00', '', number_format($item->value->amount, 2, '.', ' ')) . ' ' . $item->value->currency }}
            @if(!empty($item->value->valueAddedTaxIncluded))
                {{t('tender.tax_true')}}
            @else
                {{t('tender.tax_false')}}
            @endif
        </div>
    </div>
@endif
@if($item->status == 'complete' && isset($item->contracts[0]) && !empty($item->contracts[0]->value))
    <div class="tender_info__item">
        <div class="tender_info__label">{{ t('tender.contract_price') }}</div>
        <div class="tender_info__price">{{ str_replace('.00', '', number_format($item->__contracts_price, 2, '.', ' ')) . ' ' . $item->contracts[0]->value->currency }}</div>
    </div>
@endif
<div class="tender_info__item">
    <div class="tender_info__label">{{t('tender.assessment_purchase_conditions')}}:</div>
    <ul class="tender-stars tender-stars--{{ $rating }}">
        <li></li><li></li><li></li><li></li><li></li>
    </ul>
</div>
@if((!empty($user->is_tender_risks) || !empty($riskAccess)) && !empty($item->__risks))
<div class="tender_info__item">
    <div class="tender_info__label">{{t('tender.risks_total')}}:</div>
    <div class="risks-count"><a href="#" data-js="openRisksTab">{{ $item->__risks->risks_total }}</a></div>
</div>
@endif
<div class="user_info_icon inline-layout col-3">
    <div class="user_info_icon__item user_number @if(!sizeof($item->__active_bids)){{'number0'}}@endif" title="{{t('tender.header.bids')}}">
        {{ sizeof($item->__active_bids) }}
    </div>
    <div class="user_info_icon__item disqualifications_number @if(!$item->__count_unsuccessful_awards){{'number0'}}@endif" title="{{t('tender.header.discfalif')}}">
        {{ $item->__count_unsuccessful_awards }}
    </div>
    <div class="user_info_icon__item complaint_number @if(!sizeof($item->__complaints_complaints)){{'number0'}}@endif" title="{{t('tender.header.scarga')}}">
        {{ sizeof($item->__complaints_complaints) }}
    </div>
</div>
<div class="tender-header__review-button">
    <a style="background-color: #e55166;color:white!important;" href="" class="tender-header__link review_form_open" data-formjs="open" data-tender-edrpou="{{ $item->procuringEntity->identifier->id }}">{{t('tender.post_comment')}}</a>
</div>
@if($item->status == 'complete' && $user && $user->monitoring && $user->access_full)
    <div class="tender-header__review-button">
        <a href="{{ route('page.monitoring_tender', ['id'=>$item->tenderID,'type'=>'lots']) }}" class="tender-header__link2">{{t('tender.monitoring_lots')}}</a>
    </div>
    @if(!empty($item->__contracts_changes))
    <div class="tender-header__review-button">
        <a href="{{ route('page.monitoring_tender', ['id'=>$item->tenderID,'type'=>'changes']) }}" class="tender-header__link2">{{t('tender.monitoring_changes')}}</a>
    </div>
    @endif
@endif

    <div class="tender-header__review-button">
        <a style="background-color: #e55166;color:white!important;" class="tender-header__link2" @if(!$user){{'data-formjs=open_login'}}@else{{'href='.route('page.tender_form', ['id' => $item->tenderID, 'form' => 'F201']) }}@endif>{{t('tender.revealed_violations')}}</a>
    </div>

<div class="data_change text-center">
    {{ t('tender.last_changed') }} @tenderdate(Carbon\Carbon::createFromTimeStamp(strtotime($item->dateModified)))
</div>
