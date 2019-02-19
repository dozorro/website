@if($item)
    <tr>
        <td>
            <p>{{ $item->text }}</p>
            @if(mb_strlen($item->text) > 50)
                <div class="link-more js-more">
                    <span class="show_more">{{ t('ngo.reviews.show_all_text')}}</span>
                    <span class="hide_more">{{ t('ngo.reviews.hide_all_text')}}</span>
                </div>
            @endif
        </td>
        @if(!$for_mobile)
        <td>
            <a href="{{ route('page.ngo', ['slug' => $item->ngo_profile->slug]) }}">{{ $item->ngo_profile->title }}</a>
        </td>
        <td>
            {{ $item->tender_id }}
        </td>
        <td>
            {{ $formType == 'complete' && $item->status ? t($statuses[$item->status]) : t('ngo.review.status_new') }}
        </td>
        <td>
            {{ $item->created_at->format('d.m.y') }}
        </td>
        @endif
        @if($formType == 'new')
        <td>
            <a data-id="{{ $item->id }}" class="submit-review" style="cursor: pointer;">{{ t('ngo.reviews.verify') }}</a>
        </td>
        @endif
    </tr>
@endif
