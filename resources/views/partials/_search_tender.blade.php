@if($item)
    <tr>
        <td>
            @tenderdate($tender->date)
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
        <td>
            {{ $tender->reviews }}
        </td>
        <td>
            @if(isset($tender->tender_status) && $tender->tender_status)
                {{ (isset($dataStatus[$tender->tender_status]) && !empty($dataStatus[$tender->tender_status])) ? $dataStatus[$tender->tender_status] : $tender->tender_status }}
            @endif
        </td>
        <td>
            <span>{{ $tender->f201_count2 }}</span>
        </td>
        <td>
            @if($tender->reaction)
                {{ t('tenders.result.reaction_yes') }}
            @else
                {{ t('tenders.result.reaction_no') }}
            @endif
        </td>
        <td>
            {{ $tender->f201_count ? t('tenders.result.f201_yes') : t('tenders.result.f201_no') }}
        </td>
        @endif
    </tr>
@endif
