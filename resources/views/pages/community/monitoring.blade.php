<div class="block_header_go_short" data-monitoring>
    @foreach($monitoring as $monitor)
        @include('partials.monitoring.monitoring_info', ['profile_link' => true])
    @endforeach
</div>
</div>
@if($monitoring->isEmpty())
    <div no-reviews style="text-align: center;">{{t('community.no_results')}}</div>
@elseif($monitoring->currentPage() != $monitoring->lastPage())
    <div id="for-spinner1" class="link_pagination" data-current-page="{{$monitoring->currentPage()}}" data-last-page="{{$monitoring->lastPage()}}">{{ t('community.show_more') }}</div>
@endif