@include('partials._tenders_table', ['showReactionFilter'=>true])
@if($tenders->isEmpty())
    <div class="link_pagination" onclick="javascript:;" style="background-image: none;cursor:default;">{{ t('tenders.no_data') }}</div>
@elseif($tenders->currentPage() < $tenders->lastPage())
    <div id="for-spinner1" class="link_pagination" data-current-page="{{ $tenders->currentPage() }}" data-last-page="{{ $tenders->lastPage() }}">{{ t('tenders.show_more') }}</div>
@endif