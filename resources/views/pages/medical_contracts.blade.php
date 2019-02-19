@extends('layouts/app')

@section('content')

    @include('partials/form_medical')
    <div class="bg_white" data-js="search_medical">
        <div class="container">
            <div style="margin-top: 25px;">
                <a href="/medical_contracts_analysis" target="_blank">{{ t('page.medical_contracts_analysis') }}</a>
            </div>
            <div id="community-content" style="min-height: 400px;">
                <div class="list_tender_company">
                    <div class="overflow-table">
                        <table>
                            <thead>
                            <tr>
                                <th width="20%">{{ t('medical.result.tid') }}</th>
                                <th width="10%">{{ t('medical.result.edrpou') }}</th>
                                <th width="20%">{{ t('medical.result.product_name') }}</th>
                                <th width="20%">{{ t('medical.result.product_form') }}</th>
                                <th>{{ t('medical.result.product_q') }}</th>
                                <th>{{ t('medical.result.product_price') }}</th>
                            </tr>
                            </thead>
                            <tbody id="result">
                            @if(!empty($default))
                                {!! $default !!}
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div no-reviews style="text-align: center;">{{t('medical.result.help')}}</div>
                <div class="link_pagination @if(empty($preselected_values)){{'none'}}@endif" data-page="2">{{ t('indicators.show_more') }}</div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script type="text/javascript">

    //rating show
    $('.js_rating').barrating({
        theme: 'fontawesome-stars-o',
        initialRating: 3.5,
        readonly: true
    });

    var titleState = true;
    var ngoState = true;
    var riskState = true;
    var blocksPerRow = 1;

    $("body").on("keydown", function(e){
        var thisIndex = $("#result tr.selected").index();
        var newIndex = null;
        if(e.keyCode === 38) {
            // up
            newIndex = thisIndex - blocksPerRow;
        }
        else if(e.keyCode === 40) {
            // down
            newIndex = thisIndex + blocksPerRow;
        }
        if(newIndex !== null) {
            $("#result tr").eq(newIndex).addClass("selected").siblings().removeClass("selected");
            $("#result tr").eq(newIndex).trigger('click');
        }

    });

    $(function () {
        @if(!empty($preselected_values))
            $('#search_button').trigger('click');
        @endif

        $('#community-content').on('click', '[data-tender]', function(e) {
            e.preventDefault();

            $(this).addClass("selected").siblings().removeClass("selected");
        });
    });
</script>
@endpush
