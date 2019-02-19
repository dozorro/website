<div class="block_header_go_short" data-customers>
    @foreach($customers as $customer)
        @include('partials.customer_info')
    @endforeach
</div>
@if($customers->isEmpty())
    <div no-reviews style="text-align: center;">{{t('community.no_results')}}</div>
@elseif($customers->currentPage() != $customers->lastPage())
    <div id="for-spinner1" class="link_pagination" data-current-page="{{$customers->currentPage()}}" data-last-page="{{$customers->lastPage()}}">{{ t('community.show_more') }}</div>
@endif