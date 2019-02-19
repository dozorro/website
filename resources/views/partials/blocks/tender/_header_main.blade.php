<div class="tender-header__wrap">
    <h1 class="tender-header__h1 @if(mb_strlen($item->title) > 60) {{'maxheight'}} @endif">

        @if (in_array($item->procurementMethodType, ['aboveThresholdEU', 'competitiveDialogueEU', 'aboveThresholdUA.defense']))
            @if (App::getLocale() == 'ua')
                {{!empty($item->title) ? $item->title : t('facebook.tender_no_name')}}
                {{!empty($item->title_en) ? $item->title_en : t('facebook.tender_no_name')}}
            @elseif ((in_array($item->procurementMethodType, ['aboveThresholdEU', 'competitiveDialogueEU', 'aboveThresholdUA.defense']) && App::getLocale() == 'en'))
                {{!empty($item->title_en) ? $item->title_en : t('facebook.tender_no_name')}}
                {{!empty($item->title) ? $item->title : t('facebook.tender_no_name')}}
            @endif
        @else
            {{!empty($item->title) ? $item->title : t('facebook.tender_no_name')}}
        @endif

    </h1>
    @if(mb_strlen($item->title) > 60)
        <div class="link-more js-more">
            <span class="show_more">{{ t('tender.show_all_text')}}</span>
            <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
        </div>
    @endif

    <ul class="tender_info_header inline-layout">
        <li>
            {{t('tender.tenderID')}} {{$item->tenderID}}
        </li>
        <li>
            {{ $item->items[0]->classification->id }} - {{ $item->items[0]->classification->description }}
        </li>
        <li>
            <span class="status_tender">
                @if(!empty($dataStatus[$item->status]))
                    {{$dataStatus[$item->status]}}
                @else
                    {{$item->status}}
                @endif
            </span>
        </li>
    </ul>

    <div class="tender-description">

        <div class="row">
            <div class="col-md-6">
                <div class="tender-description__item">
                    <div class="tender-description__title">
                        {{t('tender.name_customer')}}:
                    </div>
                    <div class="tender-description__text">
                        @if(!empty($item->procuringEntity->identifier->legalName))
                            {{$item->procuringEntity->identifier->legalName}}
                        @elseif (!empty($item->procuringEntity->name))
                            {{$item->procuringEntity->name}}
                        @endif
                    </div>
                </div>
                <div class="tender-description__item">
                    <div class="tender-description__title">
                        {{t('tender.code_EDRPOU')}}:
                    </div>
                    <div class="tender-description__text">
                        @if (!empty($item->procuringEntity->identifier->id))
                            {{$item->procuringEntity->identifier->id}}
                        @else
                            {{t('tender.none')}}
                        @endif
                        @if(!empty($profileAccess) || !empty($user->is_profile_links))
                            <div style="display: inline-block;margin-left:20px;"><a target="_blank" class="profile-role1" href="{{ route('page.profile_by_id', ['scheme'=>$item->procuringEntity->identifier->scheme.'-'.$item->procuringEntity->identifier->id,'tpl'=>$profileRole1TplId,'role'=>'role1']) }}">{{ t('dozorro_profile') }}</a></div>
                        @endif
                    </div>
                </div>
                <div class="tender-description__item">
                    <div class="tender-description__title">
                        {{ t('tender.procurementMethodType') }}
                    </div>
                    <div class="tender-description__text">
                        {{ $item->__procedure_name }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tender-description__item">
                    <div class="tender-description__title">
                        {{ t('tender.contactPoint') }}
                    </div>
                    <div class="tender-description__text">
                        {{ !empty($item->procuringEntity->contactPoint->telephone) ? $item->procuringEntity->contactPoint->name : '' }},
                        {{ !empty($item->procuringEntity->contactPoint->telephone) ? $item->procuringEntity->contactPoint->telephone : '' }}
                        {{ !empty($item->procuringEntity->contactPoint->email) ? ', '.$item->procuringEntity->contactPoint->email : '' }}
                    </div>
                </div>
                <div class="tender-description__item">
                    <div class="tender-description__title">
                        {{ t('tender.procuringEntity_address') }}
                    </div>
                    <div class="tender-description__text">
                        {{ $item->procuringEntity->__address }}
                    </div>
                </div>
                @if($item->status == 'complete' && isset($item->__contracts_dateSigned) && $item->__contracts_dateSigned)
                    <div class="tender-description__item">
                        <div class="tender-description__title">
                            {{ t('tender.contract_date') }}
                        </div>
                        <div class="tender-description__text">
                            @tenderdate(Carbon\Carbon::createFromTimeStamp(strtotime($item->__contracts_dateSigned)))
                        </div>
                    </div>
                @endif
                @if($item->__active_award)
                    <div class="tender-description__item">
                        <div class="tender-description__title">
                            {{ t('tender.active_award_supplier') }}
                        </div>
                        <div class="tender-description__text">
                            @include('partials/blocks/tender/active-award-supplier')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>