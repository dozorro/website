<div class="list_tender_company">
    <h4>{{ t('tenders.result.company_tenders') }}</h4>
    @if(!empty($user->is_customer))
        <div class="export-block">
            <a href="{{ \Request::fullUrl() }}{{ strpos(\Request::fullUrl(), '?') ? '&' : '?' }}export=1" class="export"></a>
        </div>
    @endif
    <div class="overflow-table">
        <table>
            <tr>
                <th width="10%">{{ t('tenders.result.last_review') }}</th>
                <th width="25%">{{ t('tenders.result.id') }}</th>
                <th width="15%">{{ t('tenders.result.customer') }}</th>
                <th width="12%">{{ t('tenders.result.sum') }}</th>
                <th>{{ t('tenders.result.reviews') }}</th>
                <th>{{ t('tenders.result.status') }}</th>
                <th width="10%">{{ t('tenders.result.violation') }}</th>
                <th width="10%">{{ t('tenders.result.reaction') }}
                    @if($showReactionFilter)
                    <input type="checkbox" name="reaction" value="1" @if(app('request')->input('reaction') == 1) checked @endif>
                    @endif
                </th>
                <th width="10%">{{ t('tenders.result.organization') }}</th>
            </tr>

            @if(count($tenders))
                @foreach ($tenders as $item)
                    @include('partials._search_tender', [
                        'tender' => $item,
                        'item' => $item->get_tender_json(),
                        'for_mobile' => false
                        ])
                @endforeach
            @endif
        </table>
    </div>
</div>
<div class="list_tender_company mobile">
    <table>
        <tr>
            <th>{{ t('tenders.result.last_review') }}</th>
            <th width="80%"><a class="order_up" href="#">{{ t('tenders.result.id') }}</a></th>
        </tr>
        @if(count($tenders))
            @foreach ($tenders as $item)
                @include('partials._search_tender', [
                    'tender' => $item,
                    'item' => $item->get_tender_json(),
                    'for_mobile' => true
                    ])
            @endforeach
        @endif
    </table>
</div>
