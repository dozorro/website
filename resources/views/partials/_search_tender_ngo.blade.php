@if($item)
    <tr class="" data-schema="{{ $formType }}" data-id="{{ $tender->id }}">
        <td>
            @tenderdate($tender->last_date)
        </td>
        <td>
            <a href="{{ route('page.tender_by_id', ['id' => $item->tenderID]) }}">{{ $item->tenderID }}</a>
            <p>{{ $item->title }}</p>
            @if(mb_strlen($item->title) > 50)
                <div class="link-more js-more">
                    <span class="show_more">{{ t('tender.show_all_text')}}</span>
                    <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                </div>
            @endif
        </td>
        @if(!$for_mobile)
            <td>
                <a href="{{ route('search', ['search' => 'tender']) }}?edrpou={{ $item->procuringEntity->code }}">
                    @if(isset($item->procuringEntity->name))
                        {{ $item->procuringEntity->name }}
                    @endif
                </a>
                @if(isset($item->procuringEntity->name) && mb_strlen($item->procuringEntity->name) > 50)
                    <div class="link-more js-more">
                        <span class="show_more">{{ t('tender.show_all_text')}}</span>
                        <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                    </div>
                @endif
            </td>
            <td>
                {{ number_format($item->value->amount, 0, '', ' ') . ' ' . $item->value->currency }}
            </td>
            <td data-F201>
                @if(in_array($formType, ['F203','F202','F201']))
                    <p>{!! $tender->_forms_F201 !!}</p>
                    @if(mb_strlen($tender->_forms_F201) > 10)
                            <div class="link-more js-more">
                                <span class="show_more">{{ t('tender.show_all_text')}}</span>
                                <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                            </div>
                    @endif
                @endif
            </td>
            <td data-F202 class="none">
                @if(in_array($formType, ['F203','F202']))
                    <p>{!! $tender->_forms_F202 !!}</p>
                    @if(mb_strlen($tender->_forms_F202) > 10)
                            <div class="link-more js-more">
                                <span class="show_more">{{ t('tender.show_all_text')}}</span>
                                <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                            </div>
                    @endif
                @endif
            </td>
            <td data-F203 class="none">
                @if(in_array($formType, ['F203']))
                    <p>{!! $tender->_forms_F203 !!}</p>
                    @if(mb_strlen($tender->_forms_F203) > 10)
                            <div class="link-more js-more">
                                <span class="show_more">{{ t('tender.show_all_text')}}</span>
                                <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                            </div>
                    @endif
                @endif
            </td>
            <td data-F204 class="none">
                @if($formType == 'F204')
                    @if($tender->json->reason == 'defeat')
                        <span class="label_status betrayal">{{t('tender.ngo.status_defeat')}}</span>
                        <span>@tenderdate(Carbon\Carbon::createFromTimeStamp(strtotime($tender->last_date)))</span>
                    @elseif($tender->json->reason == 'succes')
                        <span class="label_status victory">{{t('tender.ngo.status_success')}}</span>
                        <span>@tenderdate(Carbon\Carbon::createFromTimeStamp(strtotime($tender->last_date)))</span>
                    @elseif($tender->json->reason == 'cancel')
                        <span class="label_status give_up">{{t('tender.ngo.status_cancel')}}</span>
                        <span>@tenderdate(Carbon\Carbon::createFromTimeStamp(strtotime($tender->last_date)))</span>
                    @endif
                @endif
            </td>
            <td data-status class="none">
                @if(isset($tender->tender_status) && $tender->tender_status)
                    {{ (isset($dataStatus[$tender->tender_status]) && !empty($dataStatus[$tender->tender_status])) ? $dataStatus[$tender->tender_status] : $tender->tender_status }}
                @endif
            </td>
            <td data-region class="none">
                @if(in_array($formType, ['region','F201','F202']))
                {{ $tender->get_region() }}
                @endif
            </td>
            <td data-moderation class="none">
                @if(in_array($formType, ['moderation']))
                    @if($tender->status == 2 && $tender->schema != 'F201')
                        <a href="{{ route('page.tender_form', ['id'=>$item->tenderID,'form'=>$tender->schema,'parentForm'=>$tender->id]) }}">{{ $tender->statusName }} <i class="fa fa-edit"></i></a>
                    @else
                        {{ $tender->statusName }}
                    @endif
                @endif
            </td>
        @endif
    </tr>
@endif