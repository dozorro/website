@if($item)
    <tr class="" data-schema="{{ $formType }}">
        <td>
            {{ \Carbon\Carbon::createFromTimestamp(strtotime($item->date))->format('H:i d.m.Y') }}
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
                <a href="{{ route('search', ['search' => 'tender']) }}?edrpou={{ $item->procuringEntity->identifier->id }}">
                    @if(isset($item->procuringEntity->name))
                        {{ $item->procuringEntity->name }}
                    @elseif(isset($item->procuringEntity->identifier->legalName))
                        {{ $item->procuringEntity->legalName }}
                    @endif
                </a>
                @if(isset($item->procuringEntity->name))
                    @if(mb_strlen($item->procuringEntity->name) > 60)
                    <div class="link-more js-more">
                        <span class="show_more">{{ t('tender.show_all_text')}}</span>
                        <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                    </div>
                    @endif
                @elseif(isset($item->procuringEntity->legalName))
                    @if(mb_strlen($item->procuringEntity->legalName) > 60)
                        <div class="link-more js-more">
                            <span class="show_more">{{ t('tender.show_all_text')}}</span>
                            <span class="hide_more">{{ t('tender.hide_all_text')}}</span>
                        </div>
                    @endif
                @endif
            </td>
            <td>
                {{ number_format($item->value->amount, 0, '', ' ') . ' ' . $item->value->currency }}
            </td>
            <td data-status>
                {{ (isset($dataStatus[$item->status])) ? $dataStatus[$item->status] : $item->status }}
            </td>
            <td data-region>
                {{ $item->procuringEntity->address->region }}
            </td>
        @endif
    </tr>
@endif