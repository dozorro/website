<div class="block_header_go_short" data-ngo>
    @foreach($ngos as $ngo)
        @include('partials.ngo_info', ['profile_link' => true, 'showBadges' => false])
    @endforeach
</div>
@if($ngos->isEmpty())
<div no-reviews style="text-align: center;">{{t('community.no_results')}}</div>
@elseif($ngos->currentPage() != $ngos->lastPage())
<div id="for-spinner1" class="link_pagination" data-current-page="{{$ngos->currentPage()}}" data-last-page="{{$ngos->lastPage()}}">{{ t('community.show_more') }}</div>
@endif